<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileFigureprofessionaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__figureprofessionali', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descrizione');
            $table->double('costo_orario_remoto');
            $table->double('costo_orario_cliente');
            $table->double('costo_orario_configurazione');
            $table->double('costo_orario_formazione_remoto');
            $table->double('costo_orario_formazione_cliente');
            $table->text('users')->nullable();
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
        Schema::dropIfExists('profile__figureprofessionali');
    }
}
