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
        Schema::create(Tables::EXERCISE_EXECUTION_POINTS, function (Blueprint $table) {
            $table->id();
            $table->text(Columns::text);
            $table->integer(Columns::index)->nullable();
            $table->foreignId(Columns::exercise_id)->constrained(Tables::EXERCISES)->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::EXERCISE_EXECUTION_POINTS);
    }
};
