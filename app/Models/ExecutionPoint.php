<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Tables;


class ExecutionPoint extends Model
{
    use HasFactory;

        protected $table = Tables::EXECUTION_POINTS;
        protected $guarded = [];
}
