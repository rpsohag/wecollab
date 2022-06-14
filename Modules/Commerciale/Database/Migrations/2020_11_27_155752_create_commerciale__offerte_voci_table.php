<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeOfferteVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__offerte_voci', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descrizione');
            $table->unsignedInteger('quantita');
            $table->double('importo_singolo', 10, 2);
            $table->double('importo', 10, 2);
            $table->double('iva');
            $table->double('importo_iva', 10, 2);
            $table->tinyInteger('esente_iva')->nullable();
            $table->tinyInteger('accettata')->nullable()->default(0);
            $table->unsignedInteger('offerta_id')->index('commerciale__offerte_voci_offerta_id_foreign');
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
        Schema::dropIfExists('commerciale__offerte_voci');
    }
}
