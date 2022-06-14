<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeFatturazioniScadenzeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__fatturazioni_scadenze', function (Blueprint $table) {
            $table->foreign('fattura_id')->references('id')->on('commerciale__fatturazioni')->onUpdate('RESTRICT')->onDelete('SET NULL');
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
            $table->dropForeign('commerciale__fatturazioni_scadenze_fattura_id_foreign');
            $table->dropForeign('commerciale__fatturazioni_scadenze_ordinativo_id_foreign');
        });
    }
}
