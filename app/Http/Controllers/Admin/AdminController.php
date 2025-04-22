<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Columns;
use App\Constants\Keys;
use App\Http\Controllers\BaseController;
use App\Models\Admin;
use App\Models\Tenant;
use App\Models\TenantInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $admin->createToken('AdminAccessToken')->accessToken;

        $data = [];
        $data[KEYS::ADMIN] = $admin;
        $data[KEYS::TOKEN] = $token;
        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        $this->setSuccessMessage('Registered successfully.');
        return $this->sendSuccessResult(code: 201);

        // return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        $token = $admin->createToken('AdminAccessToken')->accessToken;

        $data = [];
        $data[KEYS::ADMIN] = $admin;
        $data[KEYS::TOKEN] = $token;
        $this->addSuccessResultKeyValue(Keys::DATA, $data);
        $this->setSuccessMessage('Login successfully.');
        return $this->sendSuccessResult(code: 201);
    }

    public function profile(Request $request)
    {
        $admin = auth()->user();
        $this->addSuccessResultKeyValue(Keys::DATA, $admin);
        return $this->sendSuccessResult();
    }

    public function createTenant(Request $request)
    {
        $name = $request->input('name');
        $random = rand(1000, 9999); // Generates a 4-digit random number
        $dbName = env('DB_DATABASE') . '_' . str_replace(" ", "_", strtolower($name)) . '_' . $random;

        // Create DB
        \DB::statement("CREATE DATABASE `$dbName`");

        $tenant = Tenant::create([
            Columns::name => $name,
            Columns::db_name => $dbName,
            Columns::db_host => env('DB_HOST', '127.0.0.1'),
            Columns::db_user_name => env('DB_USERNAME'),
            Columns::db_password => env('DB_PASSWORD'),
        ]);

        $this->runTenantMigrations($tenant);

        // add extra column in tabel according to your requirement
        $tenant = TenantInfo::create([
            Columns::name => $name,
            Columns::tenant_id => $tenant->id,
        ]);

        $this->setSuccessMessage("Tenant created successfully");
        return $this->sendSuccessResult();
    }

    public function runTenantMigrations($tenant)
    {

        //dd($tenant);

        // Set tenant DB connection config
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => $tenant->db_host ?? env('DB_HOST', '127.0.0.1'),
            'port' => $tenant->db_port ?? env('DB_PORT', '3306'),
            'database' => $tenant->db_name,
            'username' => $tenant->db_user_name,
            'password' => $tenant->db_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        // Optionally reconnect
        \DB::purge('tenant');
        \DB::reconnect('tenant');

        // dd(config('database.connections.tenant'));

        // Now run the migrations
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => '/database/migrations/tenant',
            '--force' => true,
        ]);
    }

}
