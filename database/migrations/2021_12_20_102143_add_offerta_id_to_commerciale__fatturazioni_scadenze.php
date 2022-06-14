<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOffertaIdToCommercialeFatturazioniScadenze extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__fatturazioni_scadenze', function (Blueprint $table) {
            $table->integer('offerta_id'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__fatturazioni_scadenze', function (Blueprint $table) {
            //
        });
    }
}
