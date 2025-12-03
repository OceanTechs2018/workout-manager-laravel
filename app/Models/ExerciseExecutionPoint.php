<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseExecutionPoint extends Model
{
    use HasFactory;

    protected $table = Tables::EXERCISE_EXECUTION_POINTS;
    protected $guarded = [];

    // Relationship: each mapping belongs to an Exercise
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, Columns::exercise_id);
    }

    // Relationship: each mapping belongs to an Execution Point
    public function executionPoint()
    {
        return $this->belongsTo(ExecutionPoint::class, Columns::execution_id);
    }

}
