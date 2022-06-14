<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistAttivitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__attivita', function (Blueprint $table) {
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('richiedente_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('updated_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasklist__attivita', function (Blueprint $table) {
            $table->dropForeign('tasklist__attivita_created_user_id_foreign');
            $table->dropForeign('tasklist__attivita_richiedente_id_foreign');
            $table->dropForeign('tasklist__attivita_updated_user_id_foreign');
        });
    }
}
