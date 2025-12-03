<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Tables;
use App\Constants\Columns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(Tables::FOCUS_AREAS, function (Blueprint $table) {
            $table->string(Columns::image_url)->nullable()->after(Columns::display_name);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Tables::FOCUS_AREAS, function (Blueprint $table) {
            $table->dropColumn(Columns::image_url);
        });
    }
};
