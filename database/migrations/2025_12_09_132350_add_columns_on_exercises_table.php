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
        Schema::table(Tables::EXERCISES, function (Blueprint $table){
           $table->text(Columns::description)->nullable()->after(Columns::key_tips);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Tables::EXERCISES, function (Blueprint $table){
            $table->dropColumn(Columns::description);
        });
    }
};
