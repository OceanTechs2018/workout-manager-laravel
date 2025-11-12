<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseFocusArea extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $table = Tables::EXERCISE_FOCUS_AREAS;
    protected $guarded = [];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, Columns::exercise_id, Columns::id);
    }

    public function focusArea()
    {
        return $this->belongsTo(FocusArea::class, Columns::focus_area_id, Columns::id);
    }
}
