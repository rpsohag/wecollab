<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroUtilizzatoriUrbiToCommercialeCensimenticlienti extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__censimenticlienti', function (Blueprint $table) {
            $table->integer('numero_utilizzatori_urbi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__censimenticlienti', function (Blueprint $table) {
            //
        });
    }
}
