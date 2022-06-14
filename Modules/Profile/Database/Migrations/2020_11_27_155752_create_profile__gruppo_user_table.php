<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileGruppoUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__gruppo_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gruppo_id')->index('profile__gruppi_users_gruppo_id_foreign');
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
        Schema::dropIfExists('profile__gruppo_user');
    }
}
