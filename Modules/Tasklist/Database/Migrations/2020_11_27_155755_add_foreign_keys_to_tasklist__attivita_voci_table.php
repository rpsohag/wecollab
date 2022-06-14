<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistAttivitaVociTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__attivita_voci', function (Blueprint $table) {
            $table->foreign('attivita_id')->references('id')->on('tasklist__attivita')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasklist__attivita_voci', function (Blueprint $table) {
            $table->dropForeign('tasklist__attivita_voci_attivita_id_foreign');
        });
    }
}
