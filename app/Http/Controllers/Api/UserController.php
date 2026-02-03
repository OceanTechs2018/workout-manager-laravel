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
        // Auth::logout();
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

        // =========================================
        // 🔍 SEARCH FILTER (name, email, phone)
        // =========================================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where(Columns::name, 'LIKE', "%$keyword%")
                    ->orWhere(Columns::email, 'LIKE', "%$keyword%")
                    ->orWhere(Columns::phone, 'LIKE', "%$keyword%");
            });
        }

        // // =========================================
        // // 🔒 STATUS FILTER
        // // =========================================
        // if ($request->filled('status')) {
        //     $query->where(Columns::status, $request->status);
        // }

        // =========================================
        // 📅 DATE FILTERS (start_date, end_date)
        // =========================================
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // =========================================
        // ⏳ DURATION FILTER (today, week, month, year)
        // =========================================
        if ($request->filled('duration')) {
            switch ($request->duration) {

                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;

                case 'week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;

                case 'month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]);
                    break;

                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;

                default:
                    // Ignore if unknown value
                    break;
            }
        }

        // =========================================
        // 📄 PAGINATION HANDLING
        // page = 0 → return ALL records
        // =========================================
        if ($request->input('page', 0) == 0) {

            $users = $query->get();

            if ($users->isEmpty()) {
                $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
                return $this->sendFailResult();
            }

            $this->addSuccessResultKeyValue(Keys::DATA, $users);
            return $this->sendSuccessResult();
        }

        // Paginated response
        $limit = $request->input(Columns::limit, 10);

        $users = $query->paginate($limit);

        if ($users->isEmpty()) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::NO_DATA_FOUND);
            return $this->sendFailResult();
        }

        $this->addPaginationDataInSuccess($users);

        return $this->sendSuccessResult();
    }

    public function update(Request $request)
    {
        $authUser = auth()->user();

        if (!$authUser) {
            $this->addFailResultKeyValue(Keys::ERROR, "Unauthorized user.");
            return $this->sendFailResult();
        }

        // Validation rules for update
        $rules = [
            Columns::name => 'sometimes|string|max:255',
            Columns::email => 'sometimes|email|unique:users,email,' . $authUser->id,
            Columns::phone => 'sometimes|string|max:20|unique:users,phone,' . $authUser->id,
            Columns::image_url => 'sometimes|image|mimes:jpg,jpeg,png',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        try {

            // Clean input array
            $input = $request->only([
                Columns::name,
                Columns::email,
                Columns::phone
            ]);

            // =============================
            // HANDLE IMAGE UPLOAD
            // =============================
            if ($request->hasFile(Columns::image_url)) {

                // Delete old image if exists & not default
                if ($authUser->image_url && file_exists(public_path($authUser->image_url)) && $authUser->image_url !== 'images/users/def_user.png') {
                    unlink(public_path($authUser->image_url));
                }

                $file = $request->file(Columns::image_url);
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('users'), $fileName);

                $input[Columns::image_url] = 'users/' . $fileName;

            }

            // Update user
            $authUser->update($input);

            // Response
            $this->addSuccessResultKeyValue(Keys::DATA, $authUser);
            $this->addSuccessResultKeyValue(Keys::MESSAGE, "User updated successfully.");

            return $this->sendSuccessResult();

        } catch (\Exception $e) {

            $this->addFailResultKeyValue(Keys::ERROR, $e->getMessage());
            return $this->sendFailResult();
        }
    }

    /**
     * Delete account from their email id without auth.
     * Check condition: if any bill is pending or declined, do not delete.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deletefromemail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            Columns::email => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $email = $request->input(Columns::email);

        // Find the user by email
        $user = User::where(Columns::email, $email)->first();

        // Check if user exists
        if (!$user) {
            $this->addFailResultKeyValue(Keys::MESSAGE, Messages::USER_NOT_FOUND);
            return $this->sendFailResult();
        }

        try {
            // Perform soft delete
            $user->delete();

            $this->addSuccessResultKeyValue(Keys::MESSAGE, Messages::RECORD_DELETED_SUCCESSFULLY);
            return $this->sendSuccessResult();
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            $this->addFailResultKeyValue(Keys::MESSAGE, $e->getMessage());
            return $this->sendFailResult();
        }
    }

    /**
     * Soft Delete Authenticated User
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function softDeleteUser(Request $request)
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if user exists
        if (!$user) {
            $this->addFailResultKeyValue(Keys::ERROR, Messages::USER_NOT_FOUND);
            return $this->sendFailResult();
        }

        try {
            // Perform soft delete
            $user->delete();

            $this->addSuccessResultKeyValue(Keys::MESSAGE, Messages::RECORD_DELETED_SUCCESSFULLY);
            return $this->sendSuccessResult();
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            $this->addFailResultKeyValue(Keys::ERROR, $e->getMessage());
            return $this->sendFailResult();
        }
    }
}
