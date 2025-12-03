<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Columns;
use App\Constants\Tables;

class WorkoutExercise extends Model
{
    use HasFactory;

    protected $table = Tables::WORKOUT_EXERCISES;
    protected $guarded = [];

    /**
     * Relationship: each mapping belongs to a Workout
     */
    public function workout()
    {
        return $this->belongsTo(Workout::class, Columns::workout_id);
    }

    /**
     * Relationship: each mapping belongs to an Exercise
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, Columns::exercise_id);
    }
}
