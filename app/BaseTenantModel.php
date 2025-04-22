<?php

namespace App;

use App\Traits\Constant;
use Closure;
use Illuminate\Database\Eloquent\Model;


abstract class BaseTenantModel extends BaseModel
{
    protected $connection = 'tenant'; // Specify the connection for Tenant Model
}
