<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFocusArea extends Model
{
    use HasFactory;

    protected $table = Tables::USER_FOCUS_AREAS;
    protected $guarded = [];

    public function focusArea()
    {
        return $this->belongsTo(\App\Models\FocusArea::class, Columns::focus_area_id);
    }
}
