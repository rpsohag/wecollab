<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileAreaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__area_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('area_id')->index('profile__gruppi_users_gruppo_id_foreign');
            $table->unsignedInteger('user_id')->index('profile__gruppi_users_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile__area_user');
    }
}
