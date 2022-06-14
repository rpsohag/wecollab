<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeniStrumentaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beni_strumentali', function (Blueprint $table) {
            $table->id();
            $table->integer('tipologia');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('marca');
            $table->string('modello');
            $table->string('processore')->nullable(); 
            $table->string('hdd')->nullable();
            $table->string('memoria')->nullable();
            $table->string('serial_number');
            $table->string('imei')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('beni_strumentali');
    }
}
