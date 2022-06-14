<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAmministrazioneClientiAmbientiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('amministrazione__clienti_ambienti', function (Blueprint $table) {
            $table->foreign('cliente_id', 'amministrazione__clienti_ambienti_ibfk_1')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('amministrazione__clienti_ambienti', function (Blueprint $table) {
            $table->dropForeign('amministrazione__clienti_ambienti_ibfk_1');
        });
    }
}
