<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeOfferteVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__offerte_voci', function (Blueprint $table) {
            $table->foreign('offerta_id')->references('id')->on('commerciale__offerte')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__offerte_voci', function (Blueprint $table) {
            $table->dropForeign('commerciale__offerte_voci_offerta_id_foreign');
        });
    }
}
