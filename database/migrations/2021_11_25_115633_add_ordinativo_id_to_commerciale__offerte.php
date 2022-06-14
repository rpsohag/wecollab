<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdinativoIdToCommercialeOfferte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__offerte', function (Blueprint $table) {
            $table->integer('ordinativo_id')->unsigned();
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__offerte', function (Blueprint $table) {
            //
        });
    }
}
