<?php

use App\Constants\Columns;
use App\Constants\Tables;
use App\Models\MasterAdminRole;
use App\Models\MasterUserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Tables::MASTER_USER_ROLES, function (Blueprint $table) {
            $table->id();
            $table->string(Columns::name)->unique();
            $table->string(Columns::display_name)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default role
        DB::table(Tables::MASTER_USER_ROLES)->insert(
            [
                Columns::name => MasterUserRole::ROLE_ADMIN,
                Columns::display_name => 'Admin',
                Columns::created_at => now(),
                Columns::updated_at => now(),
            ],
        );
        DB::table(Tables::MASTER_USER_ROLES)->insert(
            [
                Columns::name => MasterUserRole::ROLE_USER,
                Columns::display_name => 'User',
                Columns::created_at => now(),
                Columns::updated_at => now(),
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Tables::MASTER_USER_ROLES);
    }
};
