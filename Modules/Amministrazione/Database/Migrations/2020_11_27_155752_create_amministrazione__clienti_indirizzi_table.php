<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmministrazioneClientiIndirizziTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amministrazione__clienti_indirizzi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('denominazione', 255);
            $table->string('nazione', 255);
            $table->string('indirizzo', 255);
            $table->string('cap', 6);
            $table->string('citta', 255);
            $table->string('provincia', 2);
            $table->string('telefono', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->unsignedInteger('cliente_id')->index('amministrazione__clienti_indirizzi_cliente_id_foreign');
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
        Schema::dropIfExists('amministrazione__clienti_indirizzi');
    }
}
