<?php

namespace App\Models;

use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGoal extends Model
{
    use HasFactory;

    protected $table = Tables::MASTER_GOALS;
    protected $guarded = [];
}
