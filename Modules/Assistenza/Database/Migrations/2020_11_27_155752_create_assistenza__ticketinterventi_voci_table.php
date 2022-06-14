<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaTicketinterventiVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistenza__ticketinterventi_voci', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('data_intervento');
            $table->text('descrizione');
            $table->unsignedInteger('quantita');
            $table->unsignedInteger('ticket_id')->index('assistenza__ticketinterventi_voci_ticket_id_foreign');
            $table->unsignedInteger('ora_inizio_1');
            $table->unsignedInteger('ora_fine_1');
            $table->unsignedInteger('ora_inizio_2');
            $table->unsignedInteger('ora_fine_2');
            $table->unsignedInteger('created_user_id');
            $table->unsignedInteger('updated_user_id');
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
        Schema::dropIfExists('assistenza__ticketinterventi_voci');
    }
}
