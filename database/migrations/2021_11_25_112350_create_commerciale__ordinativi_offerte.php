<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeOrdinativiOfferte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__ordinativi_offerte', function (Blueprint $table) {
            $table->id();
            $table->integer('ordinativo_id')->unsigned();
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi');
            $table->integer('offerta_id')->unsigned();
            $table->foreign('offerta_id')->references('id')->on('commerciale__offerte');
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
        Schema::dropIfExists('commerciale__ordinativo_offerte');
    }
}
