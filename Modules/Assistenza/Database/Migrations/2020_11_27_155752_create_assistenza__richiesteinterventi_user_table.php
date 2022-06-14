<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaRichiesteinterventiUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistenza__richiesteinterventi_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('richieste_intervento_id')->index('assistenza__richiesteinterventi_user_richiesta_id_foreign');
            $table->unsignedInteger('user_id')->index('assistenza__richiesteinterventi_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistenza__richiesteinterventi_user');
    }
}
