<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaRichiesteinterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistenza__richiesteinterventi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->unsignedInteger('numero');
            $table->unsignedInteger('cliente_id')->index('assistenza__richiesteinterventi_cliente_id_foreign');
            $table->unsignedInteger('procedura_id')->index('assistenza__richiesteinterventi_procedura_id_foreign');
            $table->unsignedInteger('area_id')->index('assistenza__richiesteinterventi_area_id_foreign');
            $table->unsignedInteger('gruppo_id')->index('assistenza__richiesteinterventi_gruppo_id_foreign');
            $table->unsignedInteger('ordinativo_id')->nullable();
            $table->string('oggetto', 255);
            $table->longText('descrizione_richiesta')->nullable();
            $table->integer('livello_urgenza');
            $table->text('motivo_urgenza')->nullable();
            $table->string('richiedente', 255)->nullable();
            $table->string('numero_da_richiamare', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->unsignedInteger('created_user_id')->index('assistenza__richiesteinterventi_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('assistenza__richiesteinterventi_updated_user_id_foreign');
            $table->timestamps();
            $table->text('secondi_lavorati')->nullable();
            $table->integer('indirizzo_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assistenza__richiesteinterventi');
    }
}
