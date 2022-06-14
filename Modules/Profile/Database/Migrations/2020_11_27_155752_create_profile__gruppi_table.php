<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileGruppiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__gruppi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->integer('area_id')->default(0);
            $table->string('email', 512)->nullable();
            $table->string('password', 255)->nullable();
            $table->timestamps();
            $table->integer('visibile_web')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile__gruppi');
    }
}
