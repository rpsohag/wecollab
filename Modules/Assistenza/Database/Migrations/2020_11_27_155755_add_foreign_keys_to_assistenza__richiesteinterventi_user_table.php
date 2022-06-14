<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssistenzaRichiesteinterventiUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__richiesteinterventi_user', function (Blueprint $table) {
            $table->foreign('richieste_intervento_id', 'assistenza__richiesteinterventi_user_ibfk_1')->references('id')->on('assistenza__richiesteinterventi')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'assistenza__richiesteinterventi_user_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assistenza__richiesteinterventi_user', function (Blueprint $table) {
            $table->dropForeign('assistenza__richiesteinterventi_user_ibfk_1');
            $table->dropForeign('assistenza__richiesteinterventi_user_ibfk_2');
        });
    }
}
