<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
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
        $query = ExerciseExecutionPoint::with([
            Relationships::EXERCISE,
            Relationships::EXECUTION_POINT
        ]);

        // Pagination optional
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
     * Store execution point IDs for an exercise.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            Columns::execution_id => 'required|array|min:1',
            Columns::execution_id . '.*' => 'integer|exists:execution_points,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exerciseId = $request->input(Columns::exercise_id);
        $executionPointIds = $request->input(Columns::execution_id);

        $inserted = [];

        foreach ($executionPointIds as $executionId) {
            $record = ExerciseExecutionPoint::create([
                Columns::exercise_id   => $exerciseId,
                Columns::execution_id  => $executionId,
            ]);

            $inserted[] = $record;
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $inserted);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution points assigned to exercise successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display a specific execution mapping.
     */
    public function show(string $id)
    {
        $data = ExerciseExecutionPoint::with([
            Relationships::EXERCISE,
            Relationships::EXECUTION_POINT
        ])->find($id);

        if (!$data) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        return $this->sendSuccessResult();
    }

    /**
     * Update execution points assigned to an exercise.
     */
    public function update(Request $request, string $exerciseId)
    {
        $rules = [
            Columns::execution_id => 'required|array|min:1',
            Columns::execution_id . '.*' => 'integer|exists:execution_points,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $newExecutionPointIds = $request->input(Columns::execution_id);

        // Fetch existing execution point IDs
        $existingIds = ExerciseExecutionPoint::where(Columns::exercise_id, $exerciseId)
            ->pluck(Columns::execution_id)
            ->toArray();

        // Determine which to add and remove
        $toAdd = array_diff($newExecutionPointIds, $existingIds);
        $toRemove = array_diff($existingIds, $newExecutionPointIds);

        // Add new execution point IDs
        foreach ($toAdd as $execId) {
            ExerciseExecutionPoint::create([
                Columns::exercise_id  => $exerciseId,
                Columns::execution_id => $execId,
            ]);
        }

        // Remove unassigned execution points
        if (!empty($toRemove)) {
            ExerciseExecutionPoint::where(Columns::exercise_id, $exerciseId)
                ->whereIn(Columns::execution_id, $toRemove)
                ->delete();
        }

        // Return updated list
        $updated = ExerciseExecutionPoint::where(Columns::exercise_id, $exerciseId)->get();

        $this->addSuccessResultKeyValue(Keys::DATA, $updated);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution points updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove a specific execution point mapping.
     */
    public function destroy(string $id)
    {
        $record = ExerciseExecutionPoint::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $record->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point removed from exercise.');
        return $this->sendSuccessResult();
    }
}
