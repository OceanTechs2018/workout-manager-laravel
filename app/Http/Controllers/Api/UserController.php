<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\Messages;
use App\Constants\ResponseCodes;
use App\Constants\Tables;
use App\Http\Controllers\BaseController;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    private $tokenName = "OceanBit";

    /**
     * Called When Token Is not Pass in Header Or Token Expire.
     */
    function unauthorised()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Unauthorised User');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When admin Services Access by none Admin User.
     */
    function adminaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Service Allow only for Admin . ');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When Active User's Services Access by none Un - Active User .
     */
    function activeaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'You don\'t have access to use this service.');
        $this->addFailResultKeyValue(Keys::DATA, Auth::user());
        return $this->sendFailResultWithCode(ResponseCodes::INACTIVE_USER);
    }

    public function register(Request $request)
    {
        $rules = User::$rules;

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        try {

            // Create clean input array
            $input = [
                Columns::name => $request->name,
                Columns::email => $request->email,
                Columns::phone => $request->phone,
            ];

            // Hash password
            $input[Columns::password] = Hash::make($request->password);

            // Remove confirm password
            unset($input[Columns::confirm_password]);

            // =============================
            // HANDLE IMAGE UPLOAD
            // =============================
            if ($request->hasFile(Columns::image_url)) {

                $file = $request->file(Columns::image_url);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in /public/users
                $file->move(public_path('users'), $fileName);

                // Store relative path in DB
                $input[Columns::image_url] = 'users/' . $fileName;

            } else {
                // Set default image if not provided
                $input[Columns::image_url] = 'images/users/def_user.png'; // Make sure this file exists in public/users
            }

            // Create user
            $user = User::create($input);

            // Generate token
            $token = $user->createToken($this->tokenName)->accessToken;

            // // Attach full image URL for API response
            // $user->image_url = $user->image_url ? asset($user->image_url) : null;

            // Response
            $this->addSuccessResultKeyValue(Keys::TOKEN, $token);
            $this->addSuccessResultKeyValue(Keys::DATA, $user);
            $this->addSuccessResultKeyValue(Keys::MESSAGE, Messages::USER_CREATED_SUCCESSFULLY);

            return $this->sendSuccessResult();

        } catch (\Exception $e) {

            $this->addFailResultKeyValue(Keys::ERROR, $e->getMessage());
            return $this->sendFailResult();
        }
    }

    function login(Request $req)
    {

        $rules = [
            Columns::email => "required|email",
            Columns::password => "required|min:6",
        ];

        /*perform validation*/
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $credentials = $req->only(Columns::email, Columns::password);

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            if ($req->has(Columns::fcm_token)) {
                $user->update([Columns::fcm_token => $req->fcm_token]);
            }

            $token = $user->createToken($this->tokenName)->accessToken;
            //$tokens = $user->tokens();
            $this->addSuccessResultKeyValue(Keys::DATA, $user);
            $this->addSuccessResultKeyValue(Keys::TOKEN, $token);
            $this->addSuccessResultKeyValue(Keys::MESSAGE, Messages::LOGIN_SUCCESSFULLY);
            return $this->sendSuccessResult();
        } else {
            $this->addFailResultKeyValue(Keys::ERROR, Messages::ERROR_INVALID_USER_ID_PASSWORD);
            return $this->sendFailResult();
        }
    }

    function logout(Request $request)
    {
        //        Auth::logout();
        $request->user()->token()->revoke();
        $this->addSuccessResultKeyValue(Keys::MESSAGE, Messages::LOGOUT_SUCCESSFULLY);
        return $this->sendSuccessResult();
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query()->latest(); // DESC order

        // If page = 0 → return all records
        if ($request->input('page', 0) == 0) {

            $users = $query->get();

            if ($users->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $users);
        } else {

            // Paginate with optional limit (default = 10)
            $limit = $request->input(Columns::limit, 10);

            $users = $query->paginate($limit);

            if ($users->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addPaginationDataInSuccess($users);
        }

        return $this->sendSuccessResult();
    }
}
