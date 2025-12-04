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
        Schema::create(Tables::EXERCISE_FOCUS_AREAS, function (Blueprint $table) {
            $table->id();
            $table->foreignId(Columns::exercise_id)->constrained(Tables::EXERCISES)->onDelete('cascade');
            $table->foreignId(Columns::focus_area_id)->constrained(Tables::FOCUS_AREAS)->onDelete('cascade');
            $table->timestamps();
            // $table->softDeletes();

            $table->unique([Columns::exercise_id, Columns::focus_area_id]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::EXERCISE_FOCUS_AREAS);
    }
};
