<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostoInternoAndImportoVenditaToProfileFigureprofessionaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile__figureprofessionali', function (Blueprint $table) {
            $table->double('costo_interno')->default(0);
            $table->double('importo_vendita')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile__figureprofessionali', function (Blueprint $table) {
            //
        });
    }
}
