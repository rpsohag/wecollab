<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaRichiesteinterventiAzioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistenza__richiesteinterventi_azioni', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descrizione')->nullable();
            $table->unsignedTinyInteger('tipo')->nullable();
            $table->integer('tipologia_intervento')->nullable();
            $table->unsignedInteger('ticket_id')->nullable()->index('assistenza__richiesteinterventi_azioni_ticket_id_foreign');
            $table->unsignedInteger('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistenza__richiesteinterventi_azioni');
    }
}
