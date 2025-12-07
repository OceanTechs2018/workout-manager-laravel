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
        Schema::create(Tables::USER_FOCUS_AREAS, function (Blueprint $table) {
            $table->id();
            $table->foreignId(Columns::user_id)->constrained(Tables::USERS)->onDelete('cascade');
            $table->foreignId(Columns::focus_area_id)->constrained(Tables::FOCUS_AREAS)->onDelete('cascade');
            $table->unique([Columns::user_id, Columns::focus_area_id]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::USER_FOCUS_AREAS);
    }
};
