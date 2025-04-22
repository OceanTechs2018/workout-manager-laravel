<?php

namespace App\Models\Tenant;

use App\BaseTenantModel;
use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthUser extends BaseTenantModel
{
    use HasFactory;

    protected $table = Tables::AUTH_USERS;

    protected $fillable = [
        Columns::user_id,
    ];
    
}
