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
        Schema::table(Tables::USER_DETAILS, function (Blueprint $table) {
            $table->foreignId(Columns::user_id)->after(Columns::id)->constrained(Tables::USERS)->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Tables::USER_DETAILS, function (Blueprint $table) {
            $table->dropColumn(Columns::user_id);
        });
    }
};
