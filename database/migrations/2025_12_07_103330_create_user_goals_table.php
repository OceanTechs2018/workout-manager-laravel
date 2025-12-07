<?php

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Tables::USER_GOALS, function (Blueprint $table) {
            $table->id();
            $table->foreignId(Columns::user_id)->constrained(Tables::USERS)->onDelete('cascade');
            $table->foreignId(Columns::goal_id)->constrained(Tables::MASTER_GOALS)->onDelete('cascade');
            $table->unique([Columns::user_id, Columns::goal_id]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::USER_GOALS);
    }
};
