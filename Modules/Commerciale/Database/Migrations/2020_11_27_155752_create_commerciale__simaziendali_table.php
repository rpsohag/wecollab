<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeSimaziendaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__simaziendali', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->string('numero_contratto', 255);
            $table->string('operatore', 255);
            $table->unsignedInteger('telefono');
            $table->integer('assegnatario');
            $table->unsignedInteger('tipo_sim');
            $table->unsignedInteger('cod_esim')->nullable();
            $table->string('iccid', 255);
            $table->string('profilo', 255)->nullable();
            $table->string('puk', 255)->nullable();
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
        Schema::dropIfExists('commerciale__simaziendali');
    }
}
