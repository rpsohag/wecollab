<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeFatturazioniVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__fatturazioni_voci', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descrizione');
            $table->unsignedInteger('quantita');
            $table->double('importo_singolo', 10, 2);
            $table->double('iva');
            $table->string('iva_tipo', 255)->nullable();
            $table->double('importo', 10, 2);
            $table->double('importo_iva', 10, 2);
            $table->tinyInteger('esente_iva')->nullable();
            $table->string('attivita_svolta', 4)->nullable();
            $table->unsignedInteger('fatturazione_id')->index('commerciale__fatturazioni_voci_fatturazione_id_foreign');
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
        Schema::dropIfExists('commerciale__fatturazioni_voci');
    }
}
