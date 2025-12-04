<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory;

    protected $table = Tables::EXERCISES;
    protected $guarded = [];

    public function focusAreas()
    {
        return $this->belongsToMany(FocusArea::class, 'exercise_focus_areas', 'exercise_id', 'focus_area_id')
        ->withPivot('exercise_id', 'focus_area_id', 'id')
            ->withTimestamps();
    }

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'exercise_equipments', 'exercise_id', 'equipment_id')->withPivot('exercise_id', 'equipment_id', 'id');
    }
}
