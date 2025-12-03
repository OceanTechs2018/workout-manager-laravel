<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\ExecutionPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExecutionPointController extends BaseController
{
    /**
     * Display a listing of execution points.
     */
    public function index(Request $request)
    {
        $query = ExecutionPoint::query();

        if ($request->input('page', 0) == 0) {

            $points = $query->latest()->get();

            if ($points->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $points);
        } else {
            $limit = $request->input(Columns::limit, 10);

            $points = $query->latest()->paginate($limit);

            if ($points->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($points);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a new execution point.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::text  => 'required|string',
            Columns::index => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $point = ExecutionPoint::create([
            Columns::text  => $request->input(Columns::text),
            Columns::index => $request->input(Columns::index),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Show a single execution point.
     */
    public function show(string $id)
    {
        $point = ExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        return $this->sendSuccessResult();
    }

    /**
     * Update a single execution point.
     */
    public function update(Request $request, string $id)
    {
        $point = ExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::text  => 'required|string',
            Columns::index => 'nullable|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $point->update([
            Columns::text  => $request->input(Columns::text),
            Columns::index => $request->input(Columns::index),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $point);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Delete an execution point.
     */
    public function destroy(string $id)
    {
        $point = ExecutionPoint::find($id);

        if (!$point) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $point->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Execution point deleted successfully.');
        return $this->sendSuccessResult();
    }
}
