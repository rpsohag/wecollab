<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaTicketinterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistenza__ticketinterventi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->dateTime('data')->useCurrent();
            $table->unsignedInteger('codice_ticket');
            $table->string('n_di_intervento');
            $table->text('descrizione_ticket');
            $table->string('materiale_consegnato')->nullable();
            $table->unsignedInteger('cliente_id')->index('assistenza__ticketinterventi_cliente_id_foreign');
            $table->unsignedInteger('ordinativo_id')->index('assistenza__ticketinterventi_ordinativo_id_foreign');
            $table->unsignedInteger('gruppo_id')->index('assistenza__ticketinterventi_gruppo_id_foreign');
            $table->unsignedInteger('tipologia_id');
            $table->unsignedInteger('settore_id');
            $table->text('note')->nullable();
            $table->unsignedInteger('created_user_id')->index('assistenza__ticketinterventi_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('assistenza__ticketinterventi_updated_user_id_foreign');
            $table->timestamps();
            $table->tinyInteger('formazione')->nullable();
            $table->tinyInteger('consulenza')->nullable();
            $table->integer('area_di_intervento_id')->nullable();
            $table->integer('procedura_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistenza__ticketinterventi');
    }
}
