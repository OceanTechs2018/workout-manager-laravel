<?php

namespace App;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    /**
     * Mass assignable attributes.
     */
    protected $guarded = [];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Validation rules for registration.
     */
    public static $rules = [
        Columns::name => "required|string",
        Columns::email => "required|email|unique:" . Tables::USERS,
        Columns::phone => "required|string|min:10|max:12|unique:" . Tables::USERS,
        Columns::password => "required|string|min:6",
        Columns::confirm_password => "required|string|same:" . Columns::password . "|min:6",
        Columns::image_url => "nullable|string",
        Columns::fcm_token => "nullable|string",
    ];
}
