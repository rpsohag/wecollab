<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileAreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__aree', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titolo', 255);
            $table->unsignedInteger('procedura_id')->index('profile__aree_procedura_id_foreign');
            $table->timestamps();
            $table->integer('gruppo_default_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile__aree');
    }
}
