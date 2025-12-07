<?php

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Tables::MASTER_GOALS, function (Blueprint $table) {
            $table->id();
            $table->string(Columns::name);
            $table->string(Columns::display_name);
            $table->boolean(Columns::status)->default(true);
            $table->timestamps();
        });

        // Insert default goals
        DB::table(Tables::MASTER_GOALS)->insert([
            [
                Columns::name => 'mobility',
                Columns::display_name => 'Mobility',
                Columns::status => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                Columns::name => 'strength_&_muscle',
                Columns::display_name => 'Strength & Muscle',
                Columns::status => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                Columns::name => 'general_fitness',
                Columns::display_name => 'General Fitness',
                Columns::status => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::MASTER_GOALS);
    }
};
