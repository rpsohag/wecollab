<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeSegnalazioniopportunitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__segnalazioniopportunita', function (Blueprint $table) {
            $table->foreign('created_user_id', 'commerciale__segnalazioneopportunita_created_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('updated_user_id', 'commerciale__segnalazioneopportunita_updated_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
            $table->dropForeign('commerciale__segnalazioneopportunita_created_user_id_foreign');
            $table->dropForeign('commerciale__segnalazioneopportunita_updated_user_id_foreign');
        });
    }
}
