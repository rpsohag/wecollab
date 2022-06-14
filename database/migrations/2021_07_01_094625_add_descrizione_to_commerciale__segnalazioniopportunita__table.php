<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescrizioneToCommercialeSegnalazioniopportunitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__segnalazioniopportunita', function (Blueprint $table) {
            $table->longText('descrizione')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__segnalazioniopportunita', function (Blueprint $table) {
            //
        });
    }
}
