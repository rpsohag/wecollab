<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssistenzaRichiesteinterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__richiesteinterventi', function (Blueprint $table) {
            $table->foreign('area_id')->references('id')->on('profile__aree')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('cliente_id')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('gruppo_id')->references('id')->on('profile__gruppi')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('procedura_id')->references('id')->on('profile__procedure')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('assistenza__richiesteinterventi', function (Blueprint $table) {
            $table->dropForeign('assistenza__richiesteinterventi_area_id_foreign');
            $table->dropForeign('assistenza__richiesteinterventi_cliente_id_foreign');
            $table->dropForeign('assistenza__richiesteinterventi_created_user_id_foreign');
            $table->dropForeign('assistenza__richiesteinterventi_gruppo_id_foreign');
            $table->dropForeign('assistenza__richiesteinterventi_procedura_id_foreign');
            $table->dropForeign('assistenza__richiesteinterventi_updated_user_id_foreign');
        });
    }
}
