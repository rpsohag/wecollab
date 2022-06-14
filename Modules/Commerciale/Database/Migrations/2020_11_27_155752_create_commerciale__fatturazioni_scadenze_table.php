<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeFatturazioniScadenzeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__fatturazioni_scadenze', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descrizione');
            $table->dateTime('data')->useCurrent();
            $table->dateTime('data_avviso');
            $table->double('importo', 10, 2)->unsigned();
            $table->unsignedInteger('ordinativo_id')->index('commerciale__fatturazioni_scadenze_ordinativo_id_foreign');
            $table->unsignedInteger('fattura_id')->nullable()->index('commerciale__fatturazioni_scadenze_fattura_id_foreign');
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
        Schema::dropIfExists('commerciale__fatturazioni_scadenze');
    }
}
