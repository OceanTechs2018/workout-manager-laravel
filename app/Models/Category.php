<?php

namespace App\Models;

use App\Constants\Columns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Tables;

class Category extends Model
{
    use HasFactory;

    protected $table = Tables::CATEGORIES;
    protected $guarded = [];

    public function workouts()
{
    return $this->belongsToMany(
        Workout::class,
        'category_workouts',
        'category_id',
        'workout_id'
    )->withPivot('id', 'category_id', 'workout_id');
}


}
