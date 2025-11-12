<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\ExerciseExecutionPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ExerciseExecutionPointController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExerciseExecutionPoint::query();

        // Optional pagination
        if ($request->input('page', 0) == 0) {
            $points = $query->latest()->get();
        } else {
            $limit = $request->input(Columns::limit, 10);
            $points = $query->latest()->paginate($limit);
        }

        if ($points->isEmpty()) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        if ($request->input('page', 0) == 0) {
            $this->addSuccessResultKeyValue(Keys::DATA, $points);
        } else {
            $this->addPaginationDataInSuccess($points);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::text        => 'required|string',
            Columns::index       => 'nullable|integer',
            Columns::exercise_id => 'required|integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $point = ExerciseExecutionPoint::create([
            Columns::text        => $request->input(Columns::text),
            Columns::index       => $request->input(Columns::index),
            Columns::exercise_id => $request->input(Columns::exercise_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $point = ExerciseExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $point = ExerciseExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::text        => 'required|string',
            Columns::index       => 'nullable|integer',
            Columns::exercise_id => 'required|integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $point->update([
            Columns::text        => $request->input(Columns::text),
            Columns::index       => $request->input(Columns::index),
            Columns::exercise_id => $request->input(Columns::exercise_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $point = ExerciseExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Soft delete
        $point->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point deleted successfully.');
        return $this->sendSuccessResult();
    }
}
