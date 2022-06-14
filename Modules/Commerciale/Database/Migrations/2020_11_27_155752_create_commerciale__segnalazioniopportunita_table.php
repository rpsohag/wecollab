<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeSegnalazioniopportunitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__segnalazioniopportunita', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->unsignedInteger('numero');
            $table->string('cliente', 255);
            $table->unsignedInteger('cliente_id')->index('commerciale__segnalazioneopportunita_cliente_id_foreign');
            $table->string('oggetto', 255);
            $table->text('checklist');
            $table->unsignedInteger('stato_id');
            $table->unsignedInteger('created_user_id')->index('commerciale__segnalazioneopportunita_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('commerciale__segnalazioneopportunita_updated_user_id_foreign');
            $table->integer('analisivendita_id')->nullable();
            $table->integer('censimento_id')->nullable();
            $table->integer('commerciale_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commerciale__segnalazioniopportunita');
    }
}
