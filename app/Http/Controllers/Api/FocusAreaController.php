<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\FocusArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FocusAreaController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FocusArea::query();

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $focusAreas = $query->latest()->get();

            if ($focusAreas->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $focusAreas);
        } else {
            // Paginate with optional limit (default 10)
            $limit = $request->input(Columns::limit, 10);
            $focusAreas = $query->latest()->paginate($limit);

            if ($focusAreas->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($focusAreas);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::name => 'required|string|max:255',
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Handle image upload
        $imageFile = $request->file(Columns::image_url);
        $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('focus_areas'), $imageFileName);
        $imagePath = 'focus_areas/' . $imageFileName;

        $focusArea = FocusArea::create([
            Columns::name => $request->input(Columns::name),
            Columns::display_name => $request->input(Columns::display_name),
            Columns::image_url => $imagePath,
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $focusArea);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Focus Area created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $focusArea = FocusArea::find($id);

        if (!$focusArea) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $focusArea);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $focusArea = FocusArea::find($id);

        if (!$focusArea) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Validation rules
        $rules = [
            Columns::name => 'required|string|max:255',
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Update name only if provided
        if ($request->filled(Columns::name)) {
            $focusArea->name = $request->input(Columns::name);
        }

        // Update display_name only if provided
        if ($request->filled(Columns::display_name)) {
            $focusArea->display_name = $request->input(Columns::display_name);
        }

        // If image is uploaded
        if ($request->hasFile(Columns::image_url)) {

            // Delete old image
            if ($focusArea->image_url && file_exists(public_path($focusArea->image_url))) {
                unlink(public_path($focusArea->image_url));
            }

            // Upload new image
            $imageFile = $request->file(Columns::image_url);
            $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('focus_areas'), $imageFileName);

            $focusArea->image_url = 'focus_areas/' . $imageFileName;
        }

        // Save changes
        $focusArea->save();

        $this->addSuccessResultKeyValue(Keys::DATA, $focusArea);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Focus Area updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $focusArea = FocusArea::find($id);

        if (!$focusArea) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $focusArea->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Focus Area deleted successfully.');
        return $this->sendSuccessResult();
    }
}
