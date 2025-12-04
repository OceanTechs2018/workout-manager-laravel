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
            $table->string(Columns::display_name);
            $table->string(Columns::image_url);
            $table->string(Columns::male_video_path)->nullable();
            $table->string(Columns::female_video_path)->nullable();
            $table->text(Columns::preparation_text)->nullable();
            $table->text(Columns::execution_point);
            $table->text(Columns::key_tips);
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::EXERCISES);
    }
};
