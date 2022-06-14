<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssistenzaRichiesteinterventiAzioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__richiesteinterventi_azioni', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('assistenza__richiesteinterventi')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assistenza__richiesteinterventi_azioni', function (Blueprint $table) {
            $table->dropForeign('assistenza__richiesteinterventi_azioni_ticket_id_foreign');
        });
    }
}
