<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Http\Request;
use Validator;

class WorkoutExerciseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Workout::with('exercises')->latest();

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
     * Store execution point IDs for an exercise.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::workout_id => 'required|integer|exists:workouts,id',
            Columns::exercise_id => 'required|array|min:1',
            Columns::exercise_id . '.*' => 'integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exerciseId = $request->input(Columns::exercise_id);
        $workoutId = $request->input(Columns::workout_id);

        $inserted = [];

        foreach ($exerciseId as $exercise) {
            $record = WorkoutExercise::create([
                Columns::exercise_id => $exercise,
                Columns::workout_id => $workoutId,
            ]);

            $inserted[] = $record;
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $inserted);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout assigned to exercise successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Update exercises assigned to a workout.
     */
    public function update(Request $request, string $workoutId)
    {
        $rules = [
            Columns::exercise_id => 'required|array|min:1',
            Columns::exercise_id . '.*' => 'integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $newExerciseIds = $request->input(Columns::exercise_id);

        // Fetch existing exercise IDs for this workout
        $existingIds = WorkoutExercise::where(Columns::workout_id, $workoutId)
            ->pluck(Columns::exercise_id)
            ->toArray();

        // Determine which to add and which to remove
        $toAdd = array_diff($newExerciseIds, $existingIds);
        $toRemove = array_diff($existingIds, $newExerciseIds);

        // Add new exercise IDs
        foreach ($toAdd as $exerciseId) {
            WorkoutExercise::create([
                Columns::workout_id => $workoutId,
                Columns::exercise_id => $exerciseId,
            ]);
        }

        // Remove exercises no longer assigned
        if (!empty($toRemove)) {
            WorkoutExercise::where(Columns::workout_id, $workoutId)
                ->whereIn(Columns::exercise_id, $toRemove)
                ->delete();
        }

        // Return updated list
        $updated = WorkoutExercise::where(Columns::workout_id, $workoutId)->get();

        $this->addSuccessResultKeyValue(Keys::DATA, $updated);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout exercises updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove a specific execution point mapping.
     */
    public function destroy(string $id)
    {
        $record = WorkoutExercise::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $record->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Workout Exercise removed from exercise.');
        return $this->sendSuccessResult();
    }

    /**
     * Display a specific execution mapping.
     */
    public function show(string $id)
    {
        $data = WorkoutExercise::with([
            Relationships::EXERCISE,
            Relationships::WORKOUT
        ])->find($id);

        if (!$data) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        return $this->sendSuccessResult();
    }

}
