<?php

namespace App\Models;

use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryWorkout extends Model
{
    use HasFactory;
    protected $table = Tables::CATEGORY_WORKOUTS;
    protected $guarded = [];

     public function workouts()
    {
        return $this->hasMany(Workout::class, 'category_id', 'id');
    }
}
