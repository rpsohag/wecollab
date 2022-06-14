<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeFatturazioniVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__fatturazioni_voci', function (Blueprint $table) {
            $table->foreign('fatturazione_id')->references('id')->on('commerciale__fatturazioni')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__fatturazioni_voci', function (Blueprint $table) {
            $table->dropForeign('commerciale__fatturazioni_voci_fatturazione_id_foreign');
        });
    }
}
