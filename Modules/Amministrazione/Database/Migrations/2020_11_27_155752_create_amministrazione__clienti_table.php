<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmministrazioneClientiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amministrazione__clienti', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda');
            $table->smallInteger('tipo');
            $table->string('ragione_sociale', 255);
            $table->string('indirizzo', 255)->nullable();
            $table->string('citta', 255)->nullable();
            $table->string('provincia', 2)->nullable();
            $table->string('cap', 5)->nullable();
            $table->string('nazione', 255)->nullable();
            $table->string('p_iva', 11)->nullable();
            $table->string('cod_fiscale', 16)->nullable();
            $table->tinyInteger('archiviato');
            $table->string('email', 255)->nullable();
            $table->string('pec', 255)->nullable();
            $table->string('codice_univoco', 255)->nullable();
            $table->binary('logo')->nullable();
            $table->longText('aree')->nullable();
            $table->string('tipologia', 255)->nullable();
            $table->tinyInteger('pa')->default(0);
            $table->string('hash_link', 255)->nullable();
            $table->integer('default_ordinativo')->nullable();
            $table->integer('commerciale_id')->nullable();
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
        Schema::dropIfExists('amministrazione__clienti');
    }
}
