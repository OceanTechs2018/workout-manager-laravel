<?php

namespace App\Models\Tenant;

use App\BaseModel;
use App\BaseTenantModel;
use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantInfo extends BaseTenantModel
{
    use HasFactory;

    protected $table = Tables::TENANT_INFO;
    protected $fillable = [
        Columns::name,
        Columns::tenant_id,
    ];
}
