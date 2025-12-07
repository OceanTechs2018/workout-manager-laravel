<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = Tables::USER_DETAILS;
    protected $guarded = [];

    public function goals()
    {
        return $this->hasMany(UserGoal::class, Columns::user_id, Columns::user_id);
    }

    public function focusAreas()
    {
        return $this->hasMany(UserFocusArea::class, Columns::user_id, Columns::user_id);
    }
}
