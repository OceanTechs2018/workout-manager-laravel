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
        Schema::table(Tables::EXERCISES, function (Blueprint $table) {
            $table->string(Columns::image_url)
                  ->nullable()
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Tables::EXERCISES, function (Blueprint $table) {
            $table->string(Columns::image_url)
                  ->nullable(false)
                  ->change();
        });
    }
};
