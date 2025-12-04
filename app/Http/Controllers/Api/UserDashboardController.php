<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\FocusArea;
use App\Models\Category;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Constants\Columns;

class UserDashboardController extends BaseController
{
    public function userHomeApi(Request $request)
    {
        // Fetch all focus areas
        $focusAreas = FocusArea::all();

        // Fetch all categories (workouts will be fetched manually, not via eager load)
        $categories = Category::all();

        $categoryData = $categories->map(function ($category) {

            // Fetch MAX 3 workouts attached with this category
            $workouts = $category->workouts()
                ->orderBy('id', 'DESC')
                ->take(3)
                ->get()
                ->map(function ($workout) {

                    // Fetch MAX 3 exercises for each workout
                    $exercises = WorkoutExercise::with('exercise')
                        ->where('workout_id', $workout->id)
                        ->take(3)
                        ->get()
                        ->map(function ($we) {

                            $ex = $we->exercise;

                            return [
                                'id' => $ex->id,
                                'name' => $ex->name,
                                'display_name' => $ex->display_name,
                                'image_url' => $ex->image_url,
                                'male_video_path' => $ex->male_video_path,
                                'female_video_path' => $ex->female_video_path,
                                'preparation_text' => $ex->preparation_text,
                                'execution_point' => $ex->execution_point,
                                'key_tips' => $ex->key_tips,
                            ];
                        });

                    return [
                        'id' => $workout->id,
                        'name' => $workout->name,
                        'display_name' => $workout->display_name,
                        'image_url' => $workout->image_url,
                        'is_popular' => $workout->is_popular,
                        'kcal_burn' => $workout->kcal_burn,
                        'time_in_min' => $workout->time_in_min,
                        'exercises' => $exercises,
                    ];
                });

            return [
                'id' => $category->id,
                'name' => $category->name,
                'display_name' => $category->display_name,
                'workouts' => $workouts,
            ];
        });

        $response = [
            'focus_areas' => $focusAreas,
            'categories' => $categoryData,
        ];

        $this->addSuccessResultKeyValue('data', $response);
        $this->addSuccessResultKeyValue('message', 'Dashboard data fetched successfully.');
        return $this->sendSuccessResult();
    }
}
