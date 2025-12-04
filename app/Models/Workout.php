<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Columns;
use App\Constants\Tables;

class Workout extends Model
{
    use HasFactory;

    protected $table = Tables::WORKOUTS;
    protected $guarded = [];

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_exercises', 'workout_id', 'exercise_id')
        ->withPivot('id', 'workout_id', 'exercise_id');
    }

    public function categories()
{
    return $this->belongsToMany(
        Category::class,
        'category_workouts',
        'workout_id',
        'category_id'
    )->withPivot('id', 'category_id', 'workout_id');
}


}
