<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAssistenzaTicketinterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__ticketinterventi', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('gruppo_id')->references('id')->on('profile__gruppi')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
        Schema::table('assistenza__ticketinterventi', function (Blueprint $table) {
            $table->dropForeign('assistenza__ticketinterventi_cliente_id_foreign');
            $table->dropForeign('assistenza__ticketinterventi_created_user_id_foreign');
            $table->dropForeign('assistenza__ticketinterventi_gruppo_id_foreign');
            $table->dropForeign('assistenza__ticketinterventi_ordinativo_id_foreign');
            $table->dropForeign('assistenza__ticketinterventi_updated_user_id_foreign');
        });
    }
}
