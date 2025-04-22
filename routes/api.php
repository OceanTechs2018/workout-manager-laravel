<?php

use App\Constants\ControllerMethods;
use App\Constants\ControllerPaths;
use App\Constants\EndPoints;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Api\UserController;
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



Route::group(["middleware" => "auth:api"], function () {

});

/**
 *
 *  unauthorised:api = Here "auth" is middleware define
 *                     in 'Kernel.php' with Class 'Authenticate.php'
 */
Route::prefix('admin')->group(function () {
    Route::post(EndPoints::login, [AdminController::class, 'login']);
    // Route::post(EndPoints::register, [AdminController::class, 'register']);
    // Route::middleware('auth:admin-api')->get('profile', [AdminController::class, 'profile']);

    Route::group(["middleware" => "auth:admin-api"], function () {
        Route::get(EndPoints::profile, [AdminController::class, 'profile']);
        Route::post(EndPoints::create_tenant, [AdminController::class, 'createTenant']);
    });
});

Route::prefix('user')->group(function () {
    Route::post(EndPoints::login, [UserController::class, 'login']);
    Route::post(EndPoints::register, [UserController::class, 'register']);
    // Route::middleware('auth:user-api')->get('profile', [UserController::class, 'profile']);

    Route::group(["middleware" => "auth:user-api"], function () {
        Route::get(EndPoints::profile, [UserController::class, 'profile']);
    });

});

/**
 * Called When Unauthorised user Access services. Called From Middleware UnauthorisedUser.php -> redirectTo()
 */
Route::get(EndPoints::unauthorised, [UserController::class, 'unauthorised'])->name(EndPoints::unauthorised);

/**
 * Called When Non Admin user Access Admin's services. Called From Middleware AdminAccess.php -> handle()
 */
Route::get(EndPoints::adminaccess, [UserController::class, 'adminaccess'])->name(EndPoints::adminaccess);

/**
 * Called When Un-Active user Access Active User services. Called From Middleware ActiveUserAccess.php -> handle()
 */
Route::get(EndPoints::activeaccess, [UserController::class, 'activeaccess'])->name(EndPoints::activeaccess);

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
