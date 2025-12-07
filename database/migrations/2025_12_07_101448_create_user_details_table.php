<?php

use App\Constants\Columns;
use App\Constants\Enums;
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
        Schema::create(Tables::USER_DETAILS, function (Blueprint $table) {
            $table->id();
            $table->enum(Columns::gender, [Enums::MALE, Enums::FEMALE]);
            $table->string(Columns::user_name);
            $table->integer(Columns::age)->default(0);
            $table->enum(Columns::current_weight_type, [Enums::KG, Enums::LBS])->default(Enums::KG);
            $table->decimal(Columns::current_weight, 10,2)->default(0.0);
            $table->enum(Columns::target_weight_type, [Enums::KG, Enums::LBS])->default(Enums::KG);
            $table->decimal(Columns::target_weight, 10,2)->default(0.0);
            $table->enum(Columns::height_type, [Enums::CM, Enums::FT])->default(Enums::CM);
            $table->decimal(Columns::height, 10,2)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::USER_DETAILS);
    }
    
};
