<?php


namespace App\Constants;


class EndPoints
{
    /**
     * ========================================================================
     * Users Services
     * ========================================================================
     */
    const user_register = '/user/register';
    const user_login = '/user/login';
    const user_forgotPassword = '/user/forgotPassword';
    const user_forgotPasswordCustom = '/user/forgotPasswordCustom';
    const user_adminUsers = '/user/adminUsers';
    const user_detail = '/user/detail';
    const user_profile = '/user/profile';
    const user_changePassword = '/user/changePassword';
    const user_updateProfile = '/user/updateProfile';
    const user_logout = '/user/logout';
    const user_list = '/user/list';
    const user_activeUsers = '/user/activeUsers';
    const user_newUsers = '/user/newUsers';
    const user_blockUsers = '/user/blockUsers';


    /**
     * ========================================================================
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

    /**
     * ========================================================================
     * Equipments Route
     * ========================================================================
     */
    const list_equipment = '/equipment';
    const show_equipment = '/equipment/{id}';
    const add_equipment = '/equipment';
    const update_equipment = '/equipment/{id}';
    const delete_equipment = '/equipment/{id}';


    /**
     * ========================================================================
     * Focus Area Route
     * ========================================================================
     */
    const list_focus_area = '/focus_area';
    const show_focus_area = '/focus_area/{id}';
    const add_focus_area = '/focus_area';
    const update_focus_area = '/focus_area/{id}';
    const delete_focus_area = '/focus_area/{id}';

    /**
     * ========================================================================
     * Exercise Route
     * ========================================================================
     */
    const list_exercise = '/exercise';
    const show_exercise = '/exercise/{id}';
    const add_exercise = '/exercise';
    const update_exercise = '/exercise/{id}';
    const delete_exercise = '/exercise/{id}';

     /**
     * ========================================================================
     * category Route
     * ========================================================================
     */
    const list_category = '/category';
    const show_category = '/category/{id}';
    const add_category = '/category';
    const update_category = '/category/{id}';
    const delete_category = '/category/{id}';

    /**
     * ========================================================================
     * category - workout Route
     * ========================================================================
     */
    const list_category_workout = '/category_workout';
    const show_category_workout = '/category_workout/{id}';
    const add_category_workout = '/category_workout';
    const update_category_workout = '/category_workout/{id}';
    const delete_category_workout = '/category_workout/{id}';

    /**
     * ========================================================================
     * Exercise Execution Point Route
     * ========================================================================
     */
    const list_exercise_execution_point = '/exercise_execution_point';
    const show_exercise_execution_point = '/exercise_execution_point/{id}';
    const add_exercise_execution_point = '/exercise_execution_point';
    const update_exercise_execution_point = '/exercise_execution_point/{id}';
    const delete_exercise_execution_point = '/exercise_execution_point/{id}';

    /**
     * ========================================================================
     * Execution Point Route
     * ========================================================================
     */
    const list_execution_point = '/execution_point';
    const show_execution_point = '/execution_point/{id}';
    const add_execution_point = '/execution_point';
    const update_execution_point = '/execution_point/{id}';
    const delete_execution_point = '/execution_point/{id}';

    /**
     * ========================================================================
     * Workout Route
     * ========================================================================
     */
    const list_workout = '/workout';
    const show_workout = '/workout/{id}';
    const add_workout = '/workout';
    const update_workout = '/workout/{id}';
    const delete_workout = '/workout/{id}';

    /**
     * ========================================================================
     * Workout-Exercise Route
     * ========================================================================
     */
    const list_workout_exercise = '/workout_exercise';
    const show_workout_exercise = '/workout_exercise/{id}';
    const add_workout_exercise = '/workout_exercise';
    const update_workout_exercise = '/workout_exercise/{id}';
    const delete_workout_exercise = '/workout_exercise/{id}';

    /**
     * ========================================================================
     * Exercise Focus Area Route
     * ========================================================================
     */
    const list_exercise_focus_area = '/exercise_focus_area';
    const show_exercise_focus_area = '/exercise_focus_area/{id}';
    const add_exercise_focus_area = '/exercise_focus_area';
    const update_exercise_focus_area = '/exercise_focus_area/{id}';
    const delete_exercise_focus_area = '/exercise_focus_area/{id}';

    /**
     * ========================================================================
     * Exercise Key Tips Route
     * ========================================================================
     */
    const list_exercise_key_tips = '/exercise_key_tips';
    const show_exercise_key_tips = '/exercise_key_tips/{id}';
    const add_exercise_key_tips = '/exercise_key_tips';
    const update_exercise_key_tips = '/exercise_key_tips/{id}';
    const delete_exercise_key_tips = '/exercise_key_tips/{id}';

    /**
     * ========================================================================
     * Exercise Equipment Route
     * ========================================================================
     */
    const list_exercise_equipment = '/exercise_equipment';
    const show_exercise_equipment = '/exercise_equipment/{id}';
    const add_exercise_equipment = '/exercise_equipment';
    const update_exercise_equipment = '/exercise_equipment/{id}';
    const delete_exercise_equipment = '/exercise_equipment/{id}';

    /**
     * ========================================================================
     * User dashboard Route
     * ========================================================================
     */
    const get_user_home = '/user-home';
    const manager_dashboard = '/manager-dashboard';
}
