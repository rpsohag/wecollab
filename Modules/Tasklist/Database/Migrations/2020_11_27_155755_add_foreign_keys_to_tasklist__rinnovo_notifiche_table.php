<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistRinnovoNotificheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__rinnovo_notifiche', function (Blueprint $table) {
            $table->foreign('rinnovo_id')->references('id')->on('tasklist__rinnovi')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasklist__rinnovo_notifiche', function (Blueprint $table) {
            $table->dropForeign('tasklist__rinnovo_notifiche_rinnovo_id_foreign');
        });
    }
}
