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
        Schema::create(Tables::WORKOUTS, function (Blueprint $table) {
            $table->id();
            $table->string(Columns::name);
            $table->string(Columns::display_name);
            $table->string(Columns::image_url);
            $table->boolean(Columns::is_popular)->default(false);
            $table->string(Columns::kcal_burn)->nullable();
            $table->integer(Columns::time_in_min);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::WORKOUTS);
    }
};
