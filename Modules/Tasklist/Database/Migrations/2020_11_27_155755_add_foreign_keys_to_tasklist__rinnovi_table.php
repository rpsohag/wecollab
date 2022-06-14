<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistRinnoviTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__rinnovi', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('tasklist__rinnovi', function (Blueprint $table) {
            $table->dropForeign('tasklist__rinnovi_cliente_id_foreign');
            $table->dropForeign('tasklist__rinnovi_created_user_id_foreign');
            $table->dropForeign('tasklist__rinnovi_updated_user_id_foreign');
        });
    }
}
