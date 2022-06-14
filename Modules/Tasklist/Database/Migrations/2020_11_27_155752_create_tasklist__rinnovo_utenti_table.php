<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistRinnovoUtentiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__rinnovo_utenti', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rinnovo_id')->index('tasklist__rinnovo_utenti_rinnovo_id_foreign');
            $table->unsignedInteger('user_id')->index('tasklist__rinnovo_utenti_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasklist__rinnovo_utenti');
    }
}
