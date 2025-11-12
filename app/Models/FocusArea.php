<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FocusArea extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $table = Tables::FOCUS_AREAS;
    protected $guarded = [];

    public function exercises()
    {
        return $this->hasMany(ExerciseFocusArea::class, Columns::focus_area_id, Columns::id);
    }
}
