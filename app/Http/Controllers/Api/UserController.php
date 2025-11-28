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
        $input = $request->all();
        $rules = User::$rules;

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        try {

            // Encrypt password
            $input[Columns::password] = Hash::make($input[Columns::password]);

            // Remove confirm password (not needed in DB)
            unset($input[Columns::confirm_password]);

            // Create user
            $user = User::create($input);

            // Generate access token
            $token = $user->createToken($this->tokenName)->accessToken;

            $user->refresh();

            // Success response
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
}
