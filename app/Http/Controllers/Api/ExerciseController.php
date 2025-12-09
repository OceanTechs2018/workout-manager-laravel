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
        $focusAreaId = $request->input('focus_area_id');

        $query = Exercise::with(['equipments', 'focusAreas'])->latest();

        if ($focusAreaId) {
            // Filter by focus area: only exercises that have this focus area
            $query = $query->whereHas('focusAreas', function ($q) use ($focusAreaId) {
                $q->where('focus_area_id', $focusAreaId);
            });
        }

        // If page=0 → return all records
        if ((int) $request->input('page', 0) === 0) {
            $exercises = $query->get();
            if ($exercises->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }
            $this->addSuccessResultKeyValue(Keys::DATA, $exercises);
            return $this->sendSuccessResult();
        }

        // Paginated results
        $limit = $request->input(Columns::limit, 10);
        $exercises = $query->paginate($limit);

        if ($exercises->isEmpty()) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addPaginationDataInSuccess($exercises);
        return $this->sendSuccessResult();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            Columns::male_video_path => 'required|file|mimes:mp4,mov,avi|max:20480',
            Columns::female_video_path => 'required|file|mimes:mp4,mov,avi|max:20480',
            Columns::preparation_text => 'nullable|string',
            Columns::execution_point => 'required|string',
            Columns::key_tips => 'required|string',
            Columns::description => 'nullable',

            // NEW VALIDATIONS
            'focus_area_ids' => 'nullable|array',
            'focus_area_ids.*' => 'integer|exists:focus_areas,id',

            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Generate name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_');

        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */
        $imageFile = $request->file(Columns::image_url);
        $imageFileName = Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
        $imageFile->move(public_path('exercises/images'), $imageFileName);
        $imagePath = 'exercises/images/' . $imageFileName;

        /*
        |--------------------------------------------------------------------------
        | Upload Videos
        |--------------------------------------------------------------------------
        */
        $maleFile = $request->file(Columns::male_video_path);
        $maleFileName = Str::uuid() . '.' . $maleFile->getClientOriginalExtension();
        $maleFile->move(public_path('exercises/male'), $maleFileName);
        $malePath = 'exercises/male/' . $maleFileName;

        $femaleFile = $request->file(Columns::female_video_path);
        $femaleFileName = Str::uuid() . '.' . $femaleFile->getClientOriginalExtension();
        $femaleFile->move(public_path('exercises/female'), $femaleFileName);
        $femalePath = 'exercises/female/' . $femaleFileName;

        /*
        |--------------------------------------------------------------------------
        | Create Exercise
        |--------------------------------------------------------------------------
        */
        $exercise = Exercise::create([
            Columns::name => $generatedName,
            Columns::display_name => $request->input(Columns::display_name),
            Columns::image_url => $imagePath,
            Columns::male_video_path => $malePath,
            Columns::female_video_path => $femalePath,
            Columns::preparation_text => $request->input(Columns::preparation_text),
            Columns::execution_point => $request->input(Columns::execution_point),
            Columns::key_tips => $request->input(Columns::key_tips),
            Columns::description => $request->input(Columns::description),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Insert Pivot: Exercise–FocusAreas
        |--------------------------------------------------------------------------
        */
        if ($request->has('focus_area_ids')) {
            $exercise->focusAreas()->sync($request->focus_area_ids);
        }

        /*
        |--------------------------------------------------------------------------
        | Insert Pivot: Exercise–Equipments
        |--------------------------------------------------------------------------
        */
        if ($request->has('equipment_ids')) {
            $exercise->equipments()->sync($request->equipment_ids);
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
        $this->addSuccessResultKeyValue(Keys::DATA, $exercise->load(['focusAreas', 'equipments']));
        $this->addSuccessResultKeyValue(Keys::MESSAGE, 'Exercise created successfully.');

        return $this->sendSuccessResult();
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exercise = Exercise::with(['equipments', 'focusAreas'])->find($id);

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
            Columns::display_name => 'required|string|max:255',
            Columns::image_url => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            Columns::male_video_path => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            Columns::female_video_path => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            Columns::preparation_text => 'nullable|string',
            Columns::execution_point => 'nullable|string',
            Columns::key_tips => 'nullable|string',
            Columns::description => 'nullable',

            // NEW VALIDATION
            'focus_area_ids' => 'nullable|array',
            'focus_area_ids.*' => 'integer|exists:focus_areas,id',

            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'integer|exists:equipments,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Auto-generate name
        $generatedName = Str::of($request->input(Columns::display_name))
            ->lower()
            ->replace(' ', '_');

        $exercise->display_name = $request->input(Columns::display_name);
        $exercise->name = $generatedName;

        /*
        |--------------------------------------------------------------------------
        | Update Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile(Columns::image_url)) {

            if ($exercise->image_url && file_exists(public_path($exercise->image_url))) {
                unlink(public_path($exercise->image_url));
            }

            $img = $request->file(Columns::image_url);
            $imgName = Str::uuid() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('exercises/images'), $imgName);

            $exercise->image_url = 'exercises/images/' . $imgName;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Male Video
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile(Columns::male_video_path)) {

            if ($exercise->male_video_path && file_exists(public_path($exercise->male_video_path))) {
                unlink(public_path($exercise->male_video_path));
            }

            $male = $request->file(Columns::male_video_path);
            $maleName = Str::uuid() . '.' . $male->getClientOriginalExtension();
            $male->move(public_path('exercises/male'), $maleName);

            $exercise->male_video_path = 'exercises/male/' . $maleName;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Female Video
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile(Columns::female_video_path)) {

            if ($exercise->female_video_path && file_exists(public_path($exercise->female_video_path))) {
                unlink(public_path($exercise->female_video_path));
            }

            $female = $request->file(Columns::female_video_path);
            $femaleName = Str::uuid() . '.' . $female->getClientOriginalExtension();
            $female->move(public_path('exercises/female'), $femaleName);

            $exercise->female_video_path = 'exercises/female/' . $femaleName;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Text Fields
        |--------------------------------------------------------------------------
        */
        $exercise->preparation_text = $request->input(Columns::preparation_text, $exercise->preparation_text);
        $exercise->execution_point = $request->input(Columns::execution_point, $exercise->execution_point);
        $exercise->key_tips = $request->input(Columns::key_tips, $exercise->key_tips);
        $exercise->description = $request->input(Columns::description, $exercise->description);

        /*
        |--------------------------------------------------------------------------
        | Save Main Exercise
        |--------------------------------------------------------------------------
        */
        $exercise->save();

        /*
        |--------------------------------------------------------------------------
        | 🔥 Sync Focus Areas (Pivot)
        |--------------------------------------------------------------------------
        */
        if ($request->has('focus_area_ids')) {
            $exercise->focusAreas()->sync($request->focus_area_ids);
        }

        /*
        |--------------------------------------------------------------------------
        | 🔥 Sync Equipments (Pivot)
        |--------------------------------------------------------------------------
        */
        if ($request->has('equipment_ids')) {
            $exercise->equipments()->sync($request->equipment_ids);
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
        $this->addSuccessResultKeyValue(Keys::DATA, $exercise->load(['focusAreas', 'equipments']));
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
