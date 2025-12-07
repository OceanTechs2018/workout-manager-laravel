<?php

namespace App\Models;

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserGoal extends Pivot
{
    use HasFactory;

    protected $table = Tables::USER_GOALS;
    protected $guarded = [];

    public function goal()
{
    return $this->belongsTo(\App\Models\MasterGoal::class, Columns::goal_id);
}

}
