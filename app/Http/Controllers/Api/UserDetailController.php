<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Enums;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\Tables;
use App\Http\Controllers\BaseController;
use App\Models\UserDetail;
use App\Models\UserGoal;
use App\Models\UserFocusArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends BaseController
{
    public function store(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::UNAUTHORIZED_USER);
            return $this->sendFailResult();
        }

        // ============================
        // CHECK IF USER DETAIL ALREADY EXISTS
        // ============================
        $existingDetail = UserDetail::where(Columns::user_id, $authUser->id)->first();

        if ($existingDetail) {
            $this->addFailResultKeyValue(Keys::MESSAGE, "User detail already exists. You cannot add again.");
            return $this->sendFailResult();
        }

        // ============================
        // VALIDATION
        // ============================
        $rules = [
            Columns::gender => 'required|in:' . Enums::MALE . ',' . Enums::FEMALE,
            Columns::user_name => 'required|string|max:255',
            Columns::age => 'required|integer|min:0',
            Columns::current_weight_type => 'required|in:' . Enums::KG . ',' . Enums::LBS,
            Columns::current_weight => 'required|numeric|min:0',
            Columns::target_weight_type => 'required|in:' . Enums::KG . ',' . Enums::LBS,
            Columns::target_weight => 'required|numeric|min:0',
            Columns::height_type => 'required|in:' . Enums::CM . ',' . Enums::FT,
            Columns::height => 'required|numeric|min:0',

            'goal_ids' => 'nullable|array',
            'goal_ids.*' => 'integer|exists:' . Tables::MASTER_GOALS . ',id',

            'focus_area_ids' => 'nullable|array',
            'focus_area_ids.*' => 'integer|exists:' . Tables::FOCUS_AREAS . ',id',

            Columns::is_notification_enable => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // ============================
        // CREATE USER DETAIL
        // ============================
        $detail = UserDetail::create([
            Columns::user_id => $authUser->id,
            Columns::gender => $request->gender,
            Columns::user_name => $request->user_name,
            Columns::age => $request->age,
            Columns::current_weight_type => $request->current_weight_type,
            Columns::current_weight => $request->current_weight,
            Columns::target_weight_type => $request->target_weight_type,
            Columns::target_weight => $request->target_weight,
            Columns::height_type => $request->height_type,
            Columns::height => $request->height,
        ]);

        // ============================
        // SYNC USER GOALS
        // ============================
        if ($request->goal_ids) {
            foreach ($request->goal_ids as $goalId) {
                UserGoal::create([
                    Columns::user_id => $authUser->id,
                    Columns::goal_id => $goalId,
                ]);
            }
        }

        // ============================
        // SYNC USER FOCUS AREAS
        // ============================
        if ($request->focus_area_ids) {
            foreach ($request->focus_area_ids as $focusId) {
                UserFocusArea::create([
                    Columns::user_id => $authUser->id,
                    Columns::focus_area_id => $focusId,
                ]);
            }
        }

        // ============================
        // UPDATE NOTIFICATION FLAG
        // ============================
        $authUser->is_notification_enable = $request->is_notification_enable;
        $authUser->save();

        // ============================
        // RESPONSE
        // ============================
        $this->addSuccessResultKeyValue(Keys::DATA, [
            'user' => $authUser,
            'user_detail' => $detail,
            'goals' => $request->goal_ids ?? [],
            'focus_areas' => $request->focus_area_ids ?? [],
        ]);

        $this->addSuccessResultKeyValue(Keys::MESSAGE, "User detail saved successfully.");

        return $this->sendSuccessResult();
    }

    public function update(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::UNAUTHORIZED_USER);
            return $this->sendFailResult();
        }

        // ============================
        // FETCH USER DETAIL
        // ============================
        $detail = UserDetail::where(Columns::user_id, $authUser->id)->first();

        if (!$detail) {
            $this->addFailResultKeyValue(Keys::MESSAGE, "User detail not found. Please create first.");
            return $this->sendFailResult();
        }

        // ============================
        // VALIDATION RULES
        // ============================
        $rules = [
                // User Detail Fields
            Columns::gender => 'required|in:' . Enums::MALE . ',' . Enums::FEMALE,
            Columns::user_name => 'required|string|max:255',
            Columns::age => 'required|integer|min:0',
            Columns::current_weight_type => 'required|in:' . Enums::KG . ',' . Enums::LBS,
            Columns::current_weight => 'required|numeric|min:0',
            Columns::target_weight_type => 'required|in:' . Enums::KG . ',' . Enums::LBS,
            Columns::target_weight => 'required|numeric|min:0',
            Columns::height_type => 'required|in:' . Enums::CM . ',' . Enums::FT,
            Columns::height => 'required|numeric|min:0',

            'goal_ids' => 'nullable|array',
            'goal_ids.*' => 'integer|exists:' . Tables::MASTER_GOALS . ',id',

            'focus_area_ids' => 'nullable|array',
            'focus_area_ids.*' => 'integer|exists:' . Tables::FOCUS_AREAS . ',id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // ============================
        // UPDATE USER DETAILS
        // ============================
        $detail->update([
            Columns::gender => $request->gender,
            Columns::user_name => $request->user_name,
            Columns::age => $request->age,
            Columns::current_weight_type => $request->current_weight_type,
            Columns::current_weight => $request->current_weight,
            Columns::target_weight_type => $request->target_weight_type,
            Columns::target_weight => $request->target_weight,
            Columns::height_type => $request->height_type,
            Columns::height => $request->height,
        ]);

        // ======================================================
        // SMART SYNC FOR GOALS
        // ======================================================
        $newGoals = $request->goal_ids ?? [];
        $existingGoals = UserGoal::where(Columns::user_id, $authUser->id)->pluck(Columns::goal_id)->toArray();

        $toRemove = array_diff($existingGoals, $newGoals);
        $toAdd = array_diff($newGoals, $existingGoals);

        if (!empty($toRemove)) {
            UserGoal::where(Columns::user_id, $authUser->id)
                ->whereIn(Columns::goal_id, $toRemove)
                ->delete();
        }

        foreach ($toAdd as $goalId) {
            UserGoal::create([
                Columns::user_id => $authUser->id,
                Columns::goal_id => $goalId,
            ]);
        }

        // ======================================================
        // SMART SYNC FOR FOCUS AREAS
        // ======================================================
        $newFocus = $request->focus_area_ids ?? [];
        $existingFocus = UserFocusArea::where(Columns::user_id, $authUser->id)->pluck(Columns::focus_area_id)->toArray();

        $focusToRemove = array_diff($existingFocus, $newFocus);
        $focusToAdd = array_diff($newFocus, $existingFocus);

        if (!empty($focusToRemove)) {
            UserFocusArea::where(Columns::user_id, $authUser->id)
                ->whereIn(Columns::focus_area_id, $focusToRemove)
                ->delete();
        }

        foreach ($focusToAdd as $focusId) {
            UserFocusArea::create([
                Columns::user_id => $authUser->id,
                Columns::focus_area_id => $focusId,
            ]);
        }

        // ============================
        // RESPONSE
        // ============================
        $this->addSuccessResultKeyValue(Keys::DATA, [
            // 'user' => $authUser,
            'user_detail' => $detail,
            'goals' => $newGoals,
            'focus_areas' => $newFocus,
        ]);

        $this->addSuccessResultKeyValue(Keys::MESSAGE, "User detail updated successfully.");

        return $this->sendSuccessResult();
    }

    public function showProfile()
    {
        $authUser = auth()->user();

        if (!$authUser) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::UNAUTHORIZED_USER);
            return $this->sendFailResult();
        }

        // ============================
        // FETCH USER DETAIL
        // ============================
        $detail = UserDetail::where(Columns::user_id, $authUser->id)->first();

        if (!$detail) {
            $this->addFailResultKeyValue(Keys::MESSAGE, "User detail not found.");
            return $this->sendFailResult();
        }

        // ============================
        // FETCH USER GOALS WITH DETAILS
        // ============================
        $goals = UserGoal::where(Columns::user_id, $authUser->id)
            ->with('goal')   // relationship required
            ->get()
            ->map(function ($g) {
                return $g->goal; // return full goal object
            });

        // ============================
        // FETCH USER FOCUS AREAS WITH DETAILS
        // ============================
        $focusAreas = UserFocusArea::where(Columns::user_id, $authUser->id)
            ->with('focusArea')  // relationship required
            ->get()
            ->map(function ($f) {
                return $f->focusArea; // return full focus area object
            });

        // ============================
        // RESPONSE
        // ============================
        $this->addSuccessResultKeyValue(Keys::DATA, [
            'user' => $authUser,
            'user_detail' => $detail,
            'goals' => $goals,
            'focus_areas' => $focusAreas,
        ]);

        return $this->sendSuccessResult();
    }

}
