<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\FocusArea;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Constants\Columns;

class UserDashboardController extends BaseController
{
    public function userHomeApi(Request $request)
    {
        // Fetch all focus areas
        $focusAreas = FocusArea::all();

        // Fetch all workouts
        $workouts = Workout::all();

        $workoutsData = $workouts->map(function ($workout) {
            // Get up to 3 exercises for this workout
            $exercises = WorkoutExercise::with('exercise')
                ->where(Columns::workout_id, $workout->id)
                ->take(3)
                ->get()
                ->map(function ($we) {
                    return $we->exercise;
                });

            return [
                'id' => $workout->id,
                'name' => $workout->name,
                'display_name' => $workout->display_name,
                'exercises' => $exercises,
            ];
        });

        $response = [
            'focus_areas' => $focusAreas,
            'workouts' => $workoutsData,
        ];

        $this->addSuccessResultKeyValue('data', $response);
        $this->addSuccessResultKeyValue('message', 'Dashboard data fetched successfully.');
        return $this->sendSuccessResult();
    }
}
