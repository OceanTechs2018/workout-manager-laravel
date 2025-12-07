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
       Schema::table(Tables::USERS, function (Blueprint $table) {
            $table->boolean(Columns::is_notification_enable)->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Tables::USERS, function (Blueprint $table) {

            $table->dropColumn(Columns::is_notification_enable);
        });
    }
};
