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
        $query = Category::query();

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $category = $query->latest()->get();

            if ($category->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $category);
        } else {
            // Paginate with optional limit (default 10)
            $limit = $request->input(Columns::limit, 10);
            $category = $query->latest()->paginate($limit);

            if ($category->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($category);
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate name from display_name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_');

        // Create record
        $category = Category::create([
            Columns::name => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Category created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

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
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate new name from display_name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_');

        // Update fields
        $category->name = $generatedName;
        $category->display_name = $request->input(Columns::display_name);

        $category->save();

        $this->addSuccessResultKeyValue(Keys::DATA, $category);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, "category updated successfully.");
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
