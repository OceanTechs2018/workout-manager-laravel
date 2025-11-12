<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseEquipment extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $table = Tables::EXERCISE_EQUIPMENTS;
    protected $guarded = [];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, Columns::exercise_id, Columns::id);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, Columns::equipment_id, Columns::id);
    }

}
