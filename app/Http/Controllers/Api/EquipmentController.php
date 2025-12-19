<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\Equipment;
use App\Models\FocusArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;


class EquipmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipment::query();

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $equipment = $query->latest()->get();

            if ($equipment->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $equipment);
        } else {
            // Paginate with optional limit (default 10)
            $limit = $request->input(Columns::limit, 10);
            $equipment = $query->latest()->paginate($limit);

            if ($equipment->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($equipment);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE NAME (SLUG)
        |--------------------------------------------------------------------------
        */
        $name = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replaceMatches('/\s+/', '_')
            ->__toString();

        // Check for duplicate
        if (Equipment::where(Columns::name, $name)->exists()) {
            $this->addFailResultKeyValue(Keys::MESSAGE, 'Equipment with this name already exists.');
            return $this->sendFailResult();
        }

        // Upload image
        $imageFile = $request->file(Columns::image_url);
        $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('equipment'), $imageFileName);
        $imagePath = 'equipment/' . $imageFileName;

        // Create record
        $equipment = Equipment::create([
            Columns::name => $name,
            Columns::display_name => $request->input(Columns::display_name),
            Columns::image_url => $imagePath,
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $equipment);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Equipment created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $equipment);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE NAME
        |--------------------------------------------------------------------------
        */
        $name = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replaceMatches('/\s+/', '_')
            ->__toString();

        // Check for duplicate (excluding current record)
        $exists = Equipment::where(Columns::name, $name)
            ->where(Columns::id, '!=', $equipment->id)
            ->exists();

        if ($exists) {
            $this->addFailResultKeyValue(Keys::MESSAGE, 'Equipment with this name already exists.');
            return $this->sendFailResult();
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPDATE (OPTIONAL)
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile(Columns::image_url)) {

            // Delete old image
            if ($equipment->image_url && file_exists(public_path($equipment->image_url))) {
                unlink(public_path($equipment->image_url));
            }

            // Upload new image
            $imageFile = $request->file(Columns::image_url);
            $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('equipment'), $imageFileName);

            $equipment->image_url = 'equipment/' . $imageFileName;
        }

        // Update fields
        $equipment->name = $name;
        $equipment->display_name = $request->input(Columns::display_name);

        $equipment->save();

        $this->addSuccessResultKeyValue(Keys::DATA, $equipment);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, "Equipment updated successfully.");
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Soft delete the record
        $equipment->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Equipment deleted successfully.');
        return $this->sendSuccessResult();
    }
}
