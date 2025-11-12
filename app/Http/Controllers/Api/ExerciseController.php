<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Http\Controllers\BaseController;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ExerciseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        if ($request->input('page', 0) == 0) {
            $exercises = $query->latest()->get();

            if ($exercises->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $exercises);
        } else {
            $limit = $request->input(Columns::limit, 10);
            $exercises = $query->latest()->paginate($limit);

            if ($exercises->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($exercises);
        }

        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::name => 'required|string|max:255',
            Columns::male_video_path => 'required|file|mimes:mp4,mov,avi|max:20480',
            Columns::female_video_path => 'required|file|mimes:mp4,mov,avi|max:20480',
            Columns::preparation_text => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Upload male video
        $maleFile = $request->file(Columns::male_video_path);
        $maleFileName = Str::uuid() . '.' . $maleFile->getClientOriginalExtension();
        $maleFile->move(public_path('male'), $maleFileName);
        $malePath = 'male/' . $maleFileName;

        // Upload female video
        $femaleFile = $request->file(Columns::female_video_path);
        $femaleFileName = Str::uuid() . '.' . $femaleFile->getClientOriginalExtension();
        $femaleFile->move(public_path('female'), $femaleFileName);
        $femalePath = 'female/' . $femaleFileName;

        // Save record
        $exercise = Exercise::create([
            Columns::name => $request->input(Columns::name),
            Columns::male_video_path => $malePath,
            Columns::female_video_path => $femalePath,
            Columns::preparation_text => $request->input(Columns::preparation_text),
        ]);

        $this->addSuccessResultKeyValue(Keys::DATA, $exercise);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise created successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addSuccessResultKeyValue(Keys::DATA, $exercise);
        return $this->sendSuccessResult();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $rules = [
            Columns::name               => 'required|string|max:255',
            Columns::male_video_path    => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            Columns::female_video_path  => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            Columns::preparation_text   => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Update male video if uploaded
        if ($request->hasFile(Columns::male_video_path)) {
            if ($exercise->male_video_path && file_exists(public_path($exercise->male_video_path))) {
                unlink(public_path($exercise->male_video_path));
            }

            $maleFile = $request->file(Columns::male_video_path);
            $maleFileName = Str::uuid() . '.' . $maleFile->getClientOriginalExtension();
            $maleFile->move(public_path('male'), $maleFileName);
            $exercise->male_video_path = 'male/' . $maleFileName;
        }

        // Update female video if uploaded
        if ($request->hasFile(Columns::female_video_path)) {
            if ($exercise->female_video_path && file_exists(public_path($exercise->female_video_path))) {
                unlink(public_path($exercise->female_video_path));
            }

            $femaleFile = $request->file(Columns::female_video_path);
            $femaleFileName = Str::uuid() . '.' . $femaleFile->getClientOriginalExtension();
            $femaleFile->move(public_path('female'), $femaleFileName);
            $exercise->female_video_path = 'female/' . $femaleFileName;
        }

        // Update other fields
        $exercise->name = $request->input(Columns::name);
        $exercise->preparation_text = $request->input(Columns::preparation_text, $exercise->preparation_text);

        $exercise->save();

        $this->addSuccessResultKeyValue(Keys::DATA, $exercise);
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise updated successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        // Delete male video if exists
        if ($exercise->male_video_path && file_exists(public_path($exercise->male_video_path))) {
            unlink(public_path($exercise->male_video_path));
        }

        // Delete female video if exists
        if ($exercise->female_video_path && file_exists(public_path($exercise->female_video_path))) {
            unlink(public_path($exercise->female_video_path));
        }

        // Soft delete the exercise record (use $exercise->forceDelete() for hard delete)
        $exercise->delete();

        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise deleted successfully.');
        return $this->sendSuccessResult();
    }
}
