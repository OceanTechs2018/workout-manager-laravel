<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\Tables;

class MasterUserRole extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = Tables::MASTER_USER_ROLES;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
}
