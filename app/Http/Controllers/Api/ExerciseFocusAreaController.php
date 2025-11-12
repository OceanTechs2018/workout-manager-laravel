<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
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
        $query = ExerciseFocusArea::with([Relationships::EXERCISE, Relationships::FOCUS_AREA]);

        // Optional pagination
        if ($request->input('page', 0) == 0) {
            $data = $query->latest()->get();
        } else {
            $limit = $request->input(Columns::limit, 10);
            $data = $query->latest()->paginate($limit);
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
            Columns::exercise_id    => 'required|integer|exists:exercises,id',
            Columns::focus_area_id  => 'required|integer|exists:focus_areas,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $record = ExerciseFocusArea::create([
            Columns::exercise_id   => $request->input(Columns::exercise_id),
            Columns::focus_area_id => $request->input(Columns::focus_area_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $record);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise focus area created successfully.');
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
            Columns::focus_area_id => 'required|integer|exists:focus_areas,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Update the record
        $record->update([
            Columns::exercise_id => $request->input(Columns::exercise_id),
            Columns::focus_area_id => $request->input(Columns::focus_area_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $record);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise focus area updated successfully.');
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
