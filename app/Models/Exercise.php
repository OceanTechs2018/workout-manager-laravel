<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = Tables::EXERCISES;
    protected $guarded = [];

    public function focusAreas()
    {
        return $this->hasMany(ExerciseFocusArea::class, Columns::exercise_id, Columns::id);
    }
}
