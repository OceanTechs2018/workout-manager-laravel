<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;


class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with('workouts'); // eager load workouts

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $categories = $query->latest()->get();

            if ($categories->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $categories);
        } else {
            // Paginate with optional limit (default 10)
            $limit = $request->input(Columns::limit, 10);
            $categories = $query->latest()->paginate($limit);

            if ($categories->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($categories);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::display_name => 'required|string|max:255',

            // NEW VALIDATION
            'workout_ids' => 'required|array',
            'workout_ids.*' => 'integer|exists:workouts,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate slug-like name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_')
            ->replaceMatches('/[^a-z0-9_]/', '');

        // Create category
        $category = Category::create([
            Columns::name => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
        ]);

        // 🔥 Sync workouts into category_workout pivot table
        $category->workouts()->sync($request->workout_ids);

        // Response
        $this->addSuccessResultKeyValue(Keys::DATA, $category->load('workouts'));
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category created and workouts attached successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('workouts')->find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::display_name => 'required|string|max:255',
            'workout_ids' => 'nullable|array',
            'workout_ids.*' => 'integer|exists:workouts,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate new slug name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_');

        // Update category
        $category->update([
            Columns::name => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
        ]);

        // Sync workouts if provided
        if ($request->has('workout_ids')) {
            $category->workouts()->sync($request->input('workout_ids'));
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $category->load('workouts'));
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Soft delete the record
        $category->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category deleted successfully.');
        return $this->sendSuccessResult();
    }
}
