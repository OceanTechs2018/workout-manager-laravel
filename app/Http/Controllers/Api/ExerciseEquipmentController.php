<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
use App\Models\ExerciseEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExerciseEquipmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExerciseEquipment::with([Relationships::EXERCISE, Relationships::EQUIPMENT]);

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
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            Columns::equipment_id => 'required|integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $record = ExerciseEquipment::create([
            Columns::exercise_id => $request->input(Columns::exercise_id),
            Columns::equipment_id => $request->input(Columns::equipment_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $record);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise equipment created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = ExerciseEquipment::with(['exercise', 'equipment'])->find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $record);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = ExerciseEquipment::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            Columns::equipment_id => 'required|integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $record->update([
            Columns::exercise_id => $request->input(Columns::exercise_id),
            Columns::equipment_id => $request->input(Columns::equipment_id),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $record);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise equipment updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = ExerciseEquipment::find($id);

        if (!$record) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $record->delete(); // soft delete

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise equipment deleted successfully.');
        return $this->sendSuccessResult();
    }
}
