<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeOrdinativiGiornateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__ordinativi_giornate', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ordinativo_id')->index('commerciale__ordinativi_giornate_ordinativo_id_foreign');
            $table->unsignedInteger('gruppo_id')->index('commerciale__ordinativi_giornate_gruppo_id_foreign');
            $table->unsignedInteger('quantita');
            $table->integer('quantita_residue');
            $table->integer('quantita_gia_effettuate')->default(0);
            $table->integer('tipo');
            $table->string('attivita', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commerciale__ordinativi_giornate');
    }
}
