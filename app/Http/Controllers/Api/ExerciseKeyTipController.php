<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
use App\Models\ExerciseKeyTip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExerciseKeyTipController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExerciseKeyTip::with(Relationships::EXERCISE);

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::text => 'required|string',
            Columns::index => 'nullable|integer',
            Columns::exercise_id => 'required|integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $keyTip = ExerciseKeyTip::create([
            Columns::text => $request->input(Columns::text),
            Columns::index => $request->input(Columns::index),
            Columns::exercise_id => $request->input(Columns::exercise_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $keyTip);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise key tip created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $keyTip = ExerciseKeyTip::with(Relationships::EXERCISE)->find($id);

        if (!$keyTip) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $keyTip);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $keyTip = ExerciseKeyTip::find($id);

        if (!$keyTip) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::text => 'required|string',
            Columns::index => 'nullable|integer',
            Columns::exercise_id => 'required|integer|exists:exercises,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $keyTip->update([
            Columns::text => $request->input(Columns::text),
            Columns::index => $request->input(Columns::index),
            Columns::exercise_id => $request->input(Columns::exercise_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $keyTip);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise key tip updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $keyTip = ExerciseKeyTip::find($id);

        if (!$keyTip) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $keyTip->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise key tip deleted successfully.');
        return $this->sendSuccessResult();
    }
}
