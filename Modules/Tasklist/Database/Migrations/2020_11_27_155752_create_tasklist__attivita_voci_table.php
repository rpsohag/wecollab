<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistAttivitaVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__attivita_voci', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descrizione');
            $table->double('durata_valore', 4, 2)->nullable();
            $table->string('durata_tipo', 255)->nullable();
            $table->dateTime('data_inizio')->nullable();
            $table->dateTime('data_fine')->nullable();
            $table->unsignedInteger('priorita');
            $table->unsignedInteger('stato');
            $table->text('users');
            $table->unsignedInteger('percentuale_completamento');
            $table->unsignedInteger('attivita_id')->index('tasklist__attivita_voci_attivita_id_foreign');
            $table->unsignedInteger('parent_id')->default(0);
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
        Schema::dropIfExists('tasklist__attivita_voci');
    }
}
