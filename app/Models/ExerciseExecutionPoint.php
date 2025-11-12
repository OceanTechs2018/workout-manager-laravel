<?php

namespace App\Models;

use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseExecutionPoint extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $table = Tables::EXERCISE_EXECUTION_POINTS;
    protected $guarded = [];
}
