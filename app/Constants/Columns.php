<?php


namespace App\Constants;


class Columns
{

    /**
     * Common Columns
     */
    const id = 'id';
    const status = 'status';
    const image_url = 'image_url';
    const record_deleted = 'record_deleted';
    const name = 'name';
    const is_active = 'is_active';
    const value = 'value';
    const old_password = "old_password";
    const new_password = 'new_password';
    const confirm_password = 'confirm_password';
    const remember_token = 'remember_token';
    const updated_at = 'updated_at';
    const created_at = 'created_at';
    const limit = 'limit';
    const page = 'page';


    /**
     * Foreigns Key Columns
     */
    const user_id = 'user_id';
    const category_id = 'category_id';
    const product_id = 'product_id';
    const setting_id = 'setting_id';
    const goal_id = 'goal_id';


    /**
     * Tables::USERS Table Columns
     */
    const first_name = 'first_name';
    const last_name = 'last_name';
    const business_name = 'business_name';
    const email = 'email';
    const phone = 'phone';
    const email_verified_at = 'email_verified_at';
    const password = 'password';
    const fcm_token = 'fcm_token';
    const is_admin = 'is_admin';
    const is_notification_enable = 'is_notification_enable';


    /**
     * Tables::PASSWORD_RESET Table Columns
     */
    const token = 'token';

    /**
     * Tables::FOCUS_AREAS Table Columns
     */
    const display_name = 'display_name';

    /**
     * Tables::EXERCISES Table Columns
     */
    const male_video_path = 'male_video_path';
    const female_video_path = 'female_video_path';
    const preparation_text = 'preparation_text';

    /**
     * Tables::EXERCISE_FOCUS_AREAS Table Columns
     */
    const exercise_id = 'exercise_id';
    const focus_area_id = 'focus_area_id';

    /**
     * Tables::EXERCISE_EXECUTION_POINTS Table Columns
     */
    const text = 'text';
    const index = 'index';

    /**
     * Tables::EXERCISE_EQUIPMENTS Table Columns
     */
    const equipment_id = 'equipment_id';

    /**
     * Tables::EXERCISE_EQUIPMENTS Table Columns
     */
    const execution_point = 'execution_point';
    const execution_id = 'execution_id';
    const key_tips = 'key_tips';

    /**
     * Tables::Workout Table Columns
     */
    const workout_id = 'workout_id';
    const kcal_burn = 'kcal_burn';
    const time_in_min = 'time_in_min';
    const is_popular = "is_popular";

    /**
     * Tables::User Details Table Columns
     */
    const gender = 'gender';
    const user_name = 'user_name';
    const age = 'age';
    const current_weight_type = 'current_weight_type';
    const target_weight_type = 'target_weight_type';
    const current_weight = 'current_weight';
    const target_weight = 'target_weight';
    const height_type = 'height_type';
    const height = 'height';
}
