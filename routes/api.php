<?php

use App\Constants\ControllerMethods;
use App\Constants\ControllerPaths;
use App\Constants\EndPoints;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ExecutionPointController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\ExerciseEquipmentController;
use App\Http\Controllers\Api\ExerciseExecutionPointController;
use App\Http\Controllers\Api\ExerciseFocusAreaController;
use App\Http\Controllers\Api\ExerciseKeyTipController;
use App\Http\Controllers\Api\FocusAreaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\WorkoutExerciseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 *
 *  unauthorised:api = Here "auth" is middleware define
 *                     in 'Kernel.php' with Class 'Authenticate.php'
 */
Route::group(["middleware" => "auth:api"], function () {

});

/**
 * Called When Unauthorised user Access services. Called From Middleware UnauthorisedUser.php -> redirectTo()
 */
Route::get(EndPoints::unauthorised, ControllerPaths::UserController . ControllerMethods::unauthorised)->name(EndPoints::unauthorised);

/**
 * Called When Non Admin user Access Admin's services. Called From Middleware AdminAccess.php -> handle()
 */
Route::get(EndPoints::adminaccess, ControllerPaths::UserController . ControllerMethods::adminaccess)->name(EndPoints::adminaccess);

/**
 * Called When Un-Active user Access Active User services. Called From Middleware ActiveUserAccess.php -> handle()
 */
Route::get(EndPoints::activeaccess, ControllerPaths::UserController . ControllerMethods::activeaccess)->name(EndPoints::activeaccess);

/**
 *  For Email Changes in ".env" File Must Follow below Steps
 *          First Check "APP_URL" in ".env" file is with port if not then add port number example -> "http://localhost:8000"
 *          1. terminate server if already started
 *          2. run command "php artisan config:cache"  For Clear Caches
 *          3. then run command "php artisan serve"  to start Server with update ".env" file config
 * */

Route::group(['middleware' => ['web']], function () {
    //routes here
    Route::get(EndPoints::password_reset, ControllerPaths::UserController . ControllerMethods::resetPassword)->name('password.reset');
    Route::get(EndPoints::password_reset . '/{token}', ControllerPaths::UserController . ControllerMethods::resetPassword)->name('password.reset');

});
Route::post(EndPoints::password_update, ControllerPaths::UserController . ControllerMethods::updatePassword)->name('password.update');

// API Route:

/**
 * ========================================================================
 * Equipment Services
 * ========================================================================
 */
Route::get(EndPoints::list_equipment, [EquipmentController::class, 'index']);
Route::get(EndPoints::show_equipment, [EquipmentController::class, 'show']);
Route::post(EndPoints::add_equipment, [EquipmentController::class, 'store']);
Route::patch(EndPoints::update_equipment, [EquipmentController::class, 'update']);
Route::delete(EndPoints::delete_equipment, [EquipmentController::class, 'destroy']);


/**
 * ========================================================================
 * Focus Area Services
 * ========================================================================
 */
Route::get(EndPoints::list_focus_area, [FocusAreaController::class, 'index']);
Route::get(EndPoints::show_focus_area, [FocusAreaController::class, 'show']);
Route::post(EndPoints::add_focus_area, [FocusAreaController::class, 'store']);
Route::patch(EndPoints::update_focus_area, [FocusAreaController::class, 'update']);
Route::delete(EndPoints::delete_focus_area, [FocusAreaController::class, 'destroy']);

/**
 * ========================================================================
 * Exercise Services
 * ========================================================================
 */
Route::get(EndPoints::list_exercise, [ExerciseController::class, 'index']);
Route::get(EndPoints::show_exercise, [ExerciseController::class, 'show']);
Route::post(EndPoints::add_exercise, [ExerciseController::class, 'store']);
Route::post(EndPoints::update_exercise, [ExerciseController::class, 'update']);
Route::delete(EndPoints::delete_exercise, [ExerciseController::class, 'destroy']);


/**
 * ========================================================================
 *  Execution Point Services
 * ========================================================================
 */
Route::get(EndPoints::list_execution_point, [ExecutionPointController::class, 'index']);
Route::get(EndPoints::show_execution_point, [ExecutionPointController::class, 'show']);
Route::post(EndPoints::add_execution_point, [ExecutionPointController::class, 'store']);
Route::post(EndPoints::update_execution_point, [ExecutionPointController::class, 'update']);
Route::delete(EndPoints::delete_execution_point, [ExecutionPointController::class, 'destroy']);


/**
 * ========================================================================
 * Exercise Execution Point Services
 * ========================================================================
 */
Route::get(EndPoints::list_exercise_execution_point, [ExerciseExecutionPointController::class, 'index']);
Route::get(EndPoints::show_exercise_execution_point, [ExerciseExecutionPointController::class, 'show']);
Route::post(EndPoints::add_exercise_execution_point, [ExerciseExecutionPointController::class, 'store']);
Route::post(EndPoints::update_exercise_execution_point, [ExerciseExecutionPointController::class, 'update']);
Route::delete(EndPoints::delete_exercise_execution_point, [ExerciseExecutionPointController::class, 'destroy']);

/**
 * ========================================================================
 * Exercise Focus Area Services
 * ========================================================================
 */
Route::get(EndPoints::list_exercise_focus_area, [ExerciseFocusAreaController::class, 'index']);
Route::get(EndPoints::show_exercise_focus_area, [ExerciseFocusAreaController::class, 'show']);
Route::post(EndPoints::add_exercise_focus_area, [ExerciseFocusAreaController::class, 'store']);
Route::post(EndPoints::update_exercise_focus_area, [ExerciseFocusAreaController::class, 'update']);
Route::delete(EndPoints::delete_exercise_focus_area, [ExerciseFocusAreaController::class, 'destroy']);

/**
 * ========================================================================
 * Exercise Key Tips Services
 * ========================================================================
 */
Route::get(EndPoints::list_exercise_key_tips, [ExerciseKeyTipController::class, 'index']);
Route::get(EndPoints::show_exercise_key_tips, [ExerciseKeyTipController::class, 'show']);
Route::post(EndPoints::add_exercise_key_tips, [ExerciseKeyTipController::class, 'store']);
Route::post(EndPoints::update_exercise_key_tips, [ExerciseKeyTipController::class, 'update']);
Route::delete(EndPoints::delete_exercise_key_tips, [ExerciseKeyTipController::class, 'destroy']);

/**
 * ========================================================================
 * Exercise Equipment Services
 * ========================================================================
 */
Route::get(EndPoints::list_exercise_equipment, [ExerciseEquipmentController::class, 'index']);
Route::get(EndPoints::show_exercise_equipment, [ExerciseEquipmentController::class, 'show']);
Route::post(EndPoints::add_exercise_equipment, [ExerciseEquipmentController::class, 'store']);
Route::post(EndPoints::update_exercise_equipment, [ExerciseEquipmentController::class, 'update']);
Route::delete(EndPoints::delete_exercise_equipment, [ExerciseEquipmentController::class, 'destroy']);

/**
 * ========================================================================
 * Workout Services
 * ========================================================================
 */
Route::get(EndPoints::list_workout, [WorkoutController::class, 'index']);
Route::get(EndPoints::show_workout, [WorkoutController::class, 'show']);
Route::post(EndPoints::add_workout, [WorkoutController::class, 'store']);
Route::post(EndPoints::update_workout, [WorkoutController::class, 'update']);
Route::delete(EndPoints::delete_workout, [WorkoutController::class, 'destroy']);

/**
 * ========================================================================
 * Workout Services
 * ========================================================================
 */
Route::get(EndPoints::list_workout_exercise, [WorkoutExerciseController::class, 'index']);
Route::get(EndPoints::show_workout_exercise, [WorkoutExerciseController::class, 'show']);
Route::post(EndPoints::add_workout_exercise, [WorkoutExerciseController::class, 'store']);
Route::post(EndPoints::update_workout_exercise, [WorkoutExerciseController::class, 'update']);
Route::delete(EndPoints::delete_workout_exercise, [WorkoutExerciseController::class, 'destroy']);

/**
 * ========================================================================
 * user Dashboard Services
 * ========================================================================
 */
Route::get(EndPoints::get_user_home, [UserDashboardController::class, 'userHomeApi']);

// User Authentication

Route::post(EndPoints::user_register, [UserController::class, 'register']);
Route::post(EndPoints::user_login, [UserController::class, 'login']);
Route::post(EndPoints::user_logout, [UserController::class, 'logout']);

Route::post('config-command', [ConfigController::class, 'runCommand']);
