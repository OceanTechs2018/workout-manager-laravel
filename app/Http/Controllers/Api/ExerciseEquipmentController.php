<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Relationships;
use App\Http\Controllers\BaseController;
use App\Models\Exercise;
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
        $query = Exercise::with('equipments')->latest();

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
            Columns::exercise_id => 'required|integer|exists:exercises,id',
            Columns::equipment_id => 'required|array|min:1',
            Columns::equipment_id . '.*' => 'integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exerciseId = $request->input(Columns::exercise_id);
        $equipmentIds = $request->input(Columns::equipment_id);

        // Prepare bulk insert data
        $insertData = [];
        foreach ($equipmentIds as $equipmentId) {
            $insertData[] = [
                Columns::exercise_id => $exerciseId,
                Columns::equipment_id => $equipmentId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert
        ExerciseEquipment::insert($insertData);

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise equipment added successfully.');
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
    public function update(Request $request, string $exerciseId)
    {
        $rules = [
            Columns::equipment_id => 'required|array|min:1',
            Columns::equipment_id . '.*' => 'integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $exercise = Exercise::find($exerciseId);

        if (!$exercise) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Incoming equipment IDs
        $newEquipmentIds = $request->input(Columns::equipment_id);

        // 🔥 Sync: add new, keep existing, remove missing
        $exercise->equipments()->sync($newEquipmentIds);

        $this->addSuccessResultKeyValue(Keys::DATA, $exercise->load('equipments'));
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
