<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistAttivitaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__attivita_user', function (Blueprint $table) {
            $table->foreign('attivita_id')->references('id')->on('tasklist__attivita')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasklist__attivita_user', function (Blueprint $table) {
            $table->dropForeign('tasklist__attivita_user_attivita_id_foreign');
            $table->dropForeign('tasklist__attivita_user_user_id_foreign');
        });
    }
}
