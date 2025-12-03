<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
use App\Models\Exercise;
use App\Models\ExerciseFocusArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ExerciseFocusAreaController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exercise::with('focusAreas')->latest();

        // Optional pagination
        if ($request->input('page', 0) == 0) {
            $data = $query->get();
        } else {
            $limit = $request->input(Columns::limit, 10);
            $data = $query->paginate($limit);
        }

        if ($data->isEmpty()) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        if ($request->input('page', 0) == 0) {
            $this->addSuccessResultKeyValue(Keys::DATA, $data);
        } else {
            $this->addPaginationDataInSuccess($data);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            Columns::focus_area_id => 'required|array|min:1',
            Columns::focus_area_id . '.*' => 'integer|exists:focus_areas,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exerciseId = $request->input(Columns::exercise_id);
        $focusAreaIds = $request->input(Columns::focus_area_id);

        $inserted = [];

        foreach ($focusAreaIds as $focusAreaId) {
            $record = ExerciseFocusArea::create([
                Columns::exercise_id => $exerciseId,
                Columns::focus_area_id => $focusAreaId,
            ]);

            $inserted[] = $record;
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $inserted);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise focus areas added successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = ExerciseFocusArea::with(['exercise', 'focusArea'])->find($id);

        // Check if record exists
        if (!$data) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Add success data
        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        return $this->sendSuccessResult();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = ExerciseFocusArea::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Validation rules
        $rules = [
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            'focus_area_ids' => 'required|array|min:1',
            'focus_area_ids.*' => 'integer|exists:focus_areas,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exerciseId = $request->input(Columns::exercise_id);
        $newFocusAreaIds = $request->input('focus_area_ids');

        // Fetch existing focus areas for this exercise
        $existingIds = ExerciseFocusArea::where(Columns::exercise_id, $exerciseId)
            ->pluck(Columns::focus_area_id)
            ->toArray();

        // Determine which to add and which to remove
        $toAdd = array_diff($newFocusAreaIds, $existingIds);
        $toRemove = array_diff($existingIds, $newFocusAreaIds);

        // Insert new focus areas
        foreach ($toAdd as $faId) {
            ExerciseFocusArea::create([
                Columns::exercise_id => $exerciseId,
                Columns::focus_area_id => $faId,
            ]);
        }

        // Remove entries that are not in the new array
        if (!empty($toRemove)) {
            ExerciseFocusArea::where(Columns::exercise_id, $exerciseId)
                ->whereIn(Columns::focus_area_id, $toRemove)
                ->delete();
        }

        // Return updated list
        $updated = ExerciseFocusArea::where(Columns::exercise_id, $exerciseId)->get();

        $this->addSuccessResultKeyValue(Keys::DATA, $updated);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise focus areas updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = ExerciseFocusArea::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Soft delete the record
        $record->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise focus area deleted successfully.');
        return $this->sendSuccessResult();
    }
}
