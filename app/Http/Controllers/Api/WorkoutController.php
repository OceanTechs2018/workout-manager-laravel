<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\Workout;
use Illuminate\Http\Request;
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
            Columns::index => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Get display_name
        $displayName = $request->input(Columns::display_name);
        $index = $request->input(Columns::index);

        // Convert to slug-like string for "name"
        $name = strtolower($displayName);          // lowercase
        $name = preg_replace('/[^a-z0-9\s]/', '', $name); // remove special chars
        $name = preg_replace('/\s+/', '_', trim($name));  // replace spaces with underscore

        $workout = Workout::create([
            Columns::name => $name,
            Columns::display_name => $displayName,
            Columns::index => $index,
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

        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::index => 'nullable|integer',
            Columns::status => 'nullable|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Get updated values
        $displayName = $request->input(Columns::display_name);
        $index = $request->input(Columns::index);
        $status = $request->input(Columns::status);

        // Convert to slug-like string for "name"
        $name = strtolower($displayName);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name); // remove special characters
        $name = preg_replace('/\s+/', '_', trim($name));  // spaces -> underscore

        // Update workout
        $workout->update([
            Columns::name => $name,
            Columns::display_name => $displayName,
            Columns::index => $index,
            Columns::status => $status
        ]);

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
