<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\MasterGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;

class MasterGoalController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterGoal::query();

        // If page=0, return all records
        if ($request->input('page', 0) == 0) {
            $goals = $query->latest()->get();

            if ($goals->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $goals);
        } 
        else {
            $limit = $request->input(Columns::limit, 10);
            $goals = $query->latest()->paginate($limit);

            if ($goals->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($goals);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::status => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Auto-generate name from display_name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_')
            ->replaceMatches('/[^a-z0-9_]/', '');

        $goal = MasterGoal::create([
            Columns::name        => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
            Columns::status      => $request->boolean(Columns::status, true),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $goal);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Goal created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = MasterGoal::find($id);

        if (!$goal) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $goal);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goal = MasterGoal::find($id);

        if (!$goal) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::status => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Re-generate name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_')
            ->replaceMatches('/[^a-z0-9_]/', '');

        $goal->update([
            Columns::name         => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
            Columns::status       => $request->boolean(Columns::status, $goal->status),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $goal);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Goal updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = MasterGoal::find($id);

        if (!$goal) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $goal->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Goal deleted successfully.');
        return $this->sendSuccessResult();
    }
}
