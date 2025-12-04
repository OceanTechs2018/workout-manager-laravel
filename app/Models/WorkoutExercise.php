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
    public function workouts()
    {
        return $this->belongsToMany(Workout::class, 'workout_exercises', 'exercise_id', 'workout_id');
    }


    /**
     * Relationship: each mapping belongs to an Exercise
     */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_exercises', 'workout_id', 'exercise_id');
    }

    public function workout()
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

}
