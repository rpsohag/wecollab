<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistAttivitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__attivita', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->unsignedInteger('richiedente_id')->index('tasklist__attivita_richiedente_id_foreign');
            $table->text('oggetto')->nullable();
            $table->text('descrizione')->nullable();
            $table->double('durata_valore', 4, 2)->nullable()->default(0.00);
            $table->string('durata_tipo', 255);
            $table->dateTime('data_inizio')->nullable();
            $table->dateTime('data_fine')->nullable();
            $table->dateTime('data_chiusura')->nullable();
            $table->unsignedInteger('priorita');
            $table->unsignedInteger('stato');
            $table->integer('cliente_id')->nullable();
            $table->unsignedInteger('percentuale_completamento');
            $table->tinyInteger('fatturazione')->nullable();
            $table->integer('ordinativo_id')->nullable();
            $table->unsignedInteger('attivitable_id')->nullable();
            $table->string('attivitable_type', 255)->nullable();
            $table->unsignedInteger('created_user_id')->index('tasklist__attivita_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('tasklist__attivita_updated_user_id_foreign');
            $table->timestamps();
            $table->integer('procedura_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->integer('gruppo_id')->nullable();
            $table->longText('supervisori_id')->nullable();
            $table->longText('prese_visioni')->nullable();
            $table->longText('pinned_by')->nullable();
            $table->longText('requisiti')->nullable();
            $table->longText('opzioni')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasklist__attivita');
    }
}
