<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Constants\Tables;
use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            Columns::email => 'required|email|unique:' . Tables::USERS . ',' . Columns::email,
            Columns::phone => 'required|string|unique:' . Tables::USERS . ',' . Columns::phone,
            Columns::password => 'required|string|min:6|confirmed',
            Columns::fcm_token => 'nullable|string',
            Columns::first_name => 'nullable|string|max:255',
            Columns::middle_name => 'nullable|string|max:255',
            Columns::last_name => 'nullable|string|max:255',
            Columns::image_url => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
            // return response()->json($validator->errors(), 422);
        }

        // Create user
        $user = User::create([
            Columns::email => $request->input(Columns::email),
            Columns::phone => $request->input(Columns::phone),
            Columns::password => Hash::make($request->input(Columns::password)),
            Columns::fcm_token => $request->input(Columns::fcm_token),
            Columns::first_name => $request->input(Columns::first_name),
            Columns::middle_name => $request->input(Columns::middle_name),
            Columns::last_name => $request->input(Columns::last_name),
            Columns::image_url => $request->input(Columns::image_url),
        ]);

        $token = $user->createToken('UserAccessToken')->accessToken;

        $data = [];
        $data[KEYS::USER] = $user;
        $data[KEYS::TOKEN] = $token;
        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        $this->setSuccessMessage('Registered successfully.');
        return $this->sendSuccessResult(code: 201);
        // return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->addFailResultKeyValue(Keys::ERROR, 'Invalid Credentials');
            return $this->sendFailResultWithCode(401);
        }

        $token = $user->createToken('UserAccessToken')->accessToken;

        $data = [];
        $data[KEYS::USER] = $user;
        $data[KEYS::TOKEN] = $token;
        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        $this->setSuccessMessage('Login successfully.');
        return $this->sendSuccessResult();
    }

    public function profile(Request $request)
    {
        $user = auth()->user();
        $this->addSuccessResultKeyValue(Keys::DATA, $user);
        return $this->sendSuccessResult();
    }

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
}
