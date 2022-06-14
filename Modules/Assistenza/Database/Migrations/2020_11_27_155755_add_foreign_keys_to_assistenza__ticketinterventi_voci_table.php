<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssistenzaTicketinterventiVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__ticketinterventi_voci', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('assistenza__ticketinterventi')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assistenza__ticketinterventi_voci', function (Blueprint $table) {
            $table->dropForeign('assistenza__ticketinterventi_voci_ticket_id_foreign');
        });
    }
}
