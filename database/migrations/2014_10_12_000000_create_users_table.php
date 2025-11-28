<?php

use App\Constants\Columns;
use App\Constants\Tables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(Tables::USERS, function (Blueprint $table) {
            $table->bigIncrements(Columns::id);
            $table->string(Columns::name)->nullable();
            $table->string(Columns::email)->unique();
            $table->string(Columns::phone, 12)->unique();
            $table->string(Columns::image_url)->nullable();
            $table->string(Columns::fcm_token)->nullable();
            $table->string(Columns::password)->nullable();
            $table->timestamp(Columns::email_verified_at)->nullable();
            $table->boolean(Columns::is_admin)->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default admin user
        DB::table(Tables::USERS)->insert([
            Columns::name => 'Admin User',
            Columns::email => 'admin@gmail.com',
            Columns::phone => '9999999999',
            Columns::password => Hash::make('123456'),
            Columns::is_admin => true,
            Columns::created_at => now(),
            Columns::updated_at => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(Tables::USERS);
    }
}
