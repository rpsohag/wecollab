<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeCensimenticlientiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__censimenticlienti', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->string('cliente', 255);
            $table->string('indirizzo', 255)->nullable();
            $table->string('cap', 5)->nullable();
            $table->string('citta', 255)->nullable();
            $table->string('provincia', 5)->nullable();
            $table->string('nazione', 255)->nullable();
            $table->string('sindaco', 255)->nullable();
            $table->string('sindaco_email', 255)->nullable();
            $table->string('sindaco_telefono', 255)->nullable();
            $table->string('segretario', 255)->nullable();
            $table->string('segretario_email', 255)->nullable();
            $table->string('segretario_telefono', 255)->nullable();
            $table->string('referente', 255)->nullable();
            $table->string('referente_email', 255)->nullable();
            $table->string('referente_telefono', 255)->nullable();
            $table->unsignedInteger('numero_dipendenti')->nullable();
            $table->unsignedInteger('fascia_abitanti');
            $table->text('referenti');
            $table->text('pianta_organica');
            $table->text('note')->nullable();
            $table->unsignedInteger('cliente_id');
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
        Schema::dropIfExists('commerciale__censimenticlienti');
    }
}
