<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeFatturazioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__fatturazioni', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda');
            $table->string('oggetto');
            $table->integer('n_fattura');
            $table->string('codice_univoco')->nullable();
            $table->string('cig')->nullable();
            $table->string('rda')->nullable();
            $table->dateTime('rda_data')->nullable();
            $table->dateTime('data');
            $table->unsignedInteger('cliente_id')->index('commerciale__fatturazioni_cliente_id_foreign');
            $table->integer('id_tipologia_fornitura')->nullable();
            $table->string('indirizzo');
            $table->tinyInteger('fepa');
            $table->tinyInteger('fattura_pa');
            $table->tinyInteger('nota_di_credito');
            $table->tinyInteger('iva_erario');
            $table->unsignedInteger('n_giorni');
            $table->integer('tipo_pagamento')->nullable();
            $table->double('acconto', 10, 2)->nullable();
            $table->double('totale_netto', 10, 2);
            $table->double('iva', 10, 2);
            $table->string('iva_natura')->nullable();
            $table->string('riferimento_normativo', 255)->nullable();
            $table->double('totale_fattura', 10, 2);
            $table->double('totale_importo_dovuto', 10, 2);
            $table->tinyInteger('iva_esigibile')->nullable();
            $table->unsignedInteger('anticipata_id');
            $table->string('iban', 50)->nullable();
            $table->unsignedInteger('ordinativo_id')->index('commerciale__fatturazioni_ordinativo_id_foreign');
            $table->tinyInteger('anticipata')->nullable();
            $table->tinyInteger('consegnata')->nullable();
            $table->tinyInteger('pagata')->nullable();
            $table->string('note', 500)->nullable();
            $table->string('attivita_svolta', 4)->nullable();
            $table->tinyInteger('nota_di_credito_interna')->nullable()->default(0);
            $table->unsignedInteger('created_user_id')->index('commerciale__fatturazioni_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('commerciale__fatturazioni_updated_user_id_foreign');
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
        Schema::dropIfExists('commerciale__fatturazioni');
    }
}
