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
        Schema::create(Tables::EXERCISES, function (Blueprint $table) {
            $table->id();
            $table->string(Columns::name);
            $table->string(Columns::male_video_path)->nullable();
            $table->string(Columns::female_video_path)->nullable();
            $table->text(Columns::preparation_text)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable(Tables::EXERCISE_EXECUTION_POINTS)) {
            Schema::table(Tables::EXERCISE_EXECUTION_POINTS, function (Blueprint $table) {
                if (Schema::hasColumn(Tables::EXERCISE_EXECUTION_POINTS, Columns::exercise_id)) {
                    $table->dropForeign([Columns::exercise_id]);
                }
            });
        }

        Schema::dropIfExists(Tables::EXERCISES);
    }
};
