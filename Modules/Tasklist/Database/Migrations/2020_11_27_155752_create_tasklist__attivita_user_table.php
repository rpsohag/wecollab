<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistAttivitaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__attivita_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('attivita_id')->index('tasklist__attivita_user_attivita_id_foreign');
            $table->unsignedInteger('user_id')->index('tasklist__attivita_user_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasklist__attivita_user');
    }
}
