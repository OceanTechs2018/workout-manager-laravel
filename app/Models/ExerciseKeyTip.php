<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseKeyTip extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $table = Tables::EXERCISE_KEY_TIPS;
    protected $guarded = [];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, Columns::exercise_id, Columns::id);
    }
}
