<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProfileProcedureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile__procedure', function (Blueprint $table) {
            $table->foreign('id', 'id_assistenza_ticketinterventi')->references('id')->on('assistenza__ticketinterventi')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile__procedure', function (Blueprint $table) {
            $table->dropForeign('id_assistenza_ticketinterventi');
        });
    }
}
