<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryWorkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryWorkoutController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with([
            'workouts' => function ($q) {
                $q->select('*'); // full workout fields
            }
        ])->latest();

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::category_id => 'required|integer|exists:categories,id',
            Columns::workout_id => 'required|array|min:1',
            Columns::workout_id . '.*' => 'integer|exists:workouts,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $categoryId = $request->input(Columns::category_id);
        $workoutIds = $request->input(Columns::workout_id);

        // Fetch existing attached workout IDs for this category
        $alreadyAttached = CategoryWorkout::where(Columns::category_id, $categoryId)
            ->pluck(Columns::workout_id)
            ->toArray();

        $inserted = [];

        foreach ($workoutIds as $workoutId) {

            // Skip if already linked
            if (in_array($workoutId, $alreadyAttached)) {
                continue;
            }

            // Insert only new pairs
            $record = CategoryWorkout::create([
                Columns::category_id => $categoryId,
                Columns::workout_id => $workoutId,
            ]);

            $inserted[] = $record;
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $inserted);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category workouts added successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Category::with(['workouts'])->find($id);

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Fetch category from URL ID
        $category = Category::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Validate incoming data
        $rules = [
            Columns::workout_id => 'required|array|min:1',
            'workout_id.*' => 'integer|exists:workouts,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Use category ID from URL only
        $categoryId = $category->id;

        // Workout IDs to update (from BODY)
        $newWorkoutIds = $request->input(Columns::workout_id);

        // Fetch existing workouts for this category
        $existingIds = CategoryWorkout::where(Columns::category_id, $categoryId)
            ->pluck(Columns::workout_id)
            ->toArray();

        // Determine what to add and remove
        $toAdd = array_diff($newWorkoutIds, $existingIds);
        $toRemove = array_diff($existingIds, $newWorkoutIds);

        // Add new workouts
        foreach ($toAdd as $workoutId) {
            CategoryWorkout::create([
                Columns::category_id => $categoryId,
                Columns::workout_id => $workoutId,
            ]);
        }

        // Remove workouts not in new list
        if (!empty($toRemove)) {
            CategoryWorkout::where(Columns::category_id, $categoryId)
                ->whereIn(Columns::workout_id, $toRemove)
                ->delete();
        }

        // Fetch updated list
        $updated = CategoryWorkout::where(Columns::category_id, $categoryId)->get();

        $this->addSuccessResultKeyValue(Keys::DATA, $updated);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category workout areas updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = CategoryWorkout::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Soft delete the record
        $record->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category Wokout deleted successfully.');
        return $this->sendSuccessResult();
    }
}