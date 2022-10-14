<?php

use App\Constants\ControllerMethods;
use App\Constants\ControllerPaths;
use App\Constants\EndPoints;
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
