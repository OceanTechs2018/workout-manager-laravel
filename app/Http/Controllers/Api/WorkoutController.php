<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\Workout;
use Illuminate\Http\Request;
use Str;
use Validator;

class WorkoutController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Workout::query();

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $workout = $query->latest()->get();

            if ($workout->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $workout);
        } else {
            // Paginate with optional limit (default 10)
            $limit = $request->input(Columns::limit, 10);
            $workout = $query->latest()->paginate($limit);

            if ($workout->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($workout);
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
            Columns::image_url => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            Columns::is_popular => 'nullable|boolean',
            Columns::kcal_burn => 'nullable|string',
            Columns::time_in_min => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate name from display_name
        $displayName = $request->input(Columns::display_name);
        $name = strtolower($displayName);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name); // remove special characters
        $name = preg_replace('/\s+/', '_', trim($name));  // replace spaces with underscore

        // Upload Image
        $imageFile = $request->file(Columns::image_url);
        $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('workouts'), $imageFileName);
        $imagePath = 'workouts/' . $imageFileName;

        // Create Workout
        $workout = Workout::create([
            Columns::name => $name,
            Columns::display_name => $displayName,
            Columns::image_url => $imagePath,
            Columns::is_popular => $request->input(Columns::is_popular, false),
            Columns::kcal_burn => $request->input(Columns::kcal_burn),
            Columns::time_in_min => $request->input(Columns::time_in_min),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $workout);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workout = Workout::find($id);

        if (!$workout) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Validation
        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            Columns::is_popular => 'nullable|boolean',
            Columns::kcal_burn => 'nullable|string|max:50',
            Columns::time_in_min => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Convert display_name → slug name
        $displayName = $request->input(Columns::display_name);
        $name = strtolower($displayName);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        $name = preg_replace('/\s+/', '_', trim($name));

        // Handle image update
        if ($request->hasFile(Columns::image_url)) {

            // Delete old image
            if ($workout->image_url && file_exists(public_path($workout->image_url))) {
                unlink(public_path($workout->image_url));
            }

            // Upload new image
            $imageFile = $request->file(Columns::image_url);
            $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('workouts'), $imageFileName);

            $workout->image_url = 'workouts/' . $imageFileName;
        }

        // Update fields
        $workout->name = $name;
        $workout->display_name = $displayName;

        if ($request->filled(Columns::kcal_burn)) {
            $workout->kcal_burn = $request->input(Columns::kcal_burn);
        }

        if ($request->filled(Columns::time_in_min)) {
            $workout->time_in_min = $request->input(Columns::time_in_min);
        }

        if (!is_null($request->input(Columns::is_popular))) {
            $workout->is_popular = $request->boolean(Columns::is_popular);
        }

        $workout->save();

        $this->addSuccessResultKeyValue(Keys::DATA, $workout);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout updated successfully.');
        return $this->sendSuccessResult();
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workout = Workout::find($id);

        if (!$workout) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $workout);
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workout = Workout::find($id);

        if (!$workout) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $workout->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout deleted successfully.');
        return $this->sendSuccessResult();
    }

}
