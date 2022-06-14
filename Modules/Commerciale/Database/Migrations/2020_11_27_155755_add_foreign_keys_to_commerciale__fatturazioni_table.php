<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeFatturazioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__fatturazioni', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__fatturazioni', function (Blueprint $table) {
            $table->dropForeign('commerciale__fatturazioni_cliente_id_foreign');
            $table->dropForeign('commerciale__fatturazioni_created_user_id_foreign');
            $table->dropForeign('commerciale__fatturazioni_updated_user_id_foreign');
        });
    }
}
