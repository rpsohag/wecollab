<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasklistTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist__timesheets', function (Blueprint $table) {
            $table->foreign('area_id')->references('id')->on('profile__aree')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('attivita_id')->references('id')->on('tasklist__attivita')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('cliente_id')->references('id')->on('amministrazione__clienti')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('created_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('gruppo_id')->references('id')->on('profile__gruppi')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('procedura_id')->references('id')->on('profile__procedure')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('ticket_azione_id')->references('id')->on('assistenza__richiesteinterventi_azioni')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('tasklist__timesheets', function (Blueprint $table) {
            $table->dropForeign('tasklist__timesheets_area_id_foreign');
            $table->dropForeign('tasklist__timesheets_attivita_id_foreign');
            $table->dropForeign('tasklist__timesheets_cliente_id_foreign');
            $table->dropForeign('tasklist__timesheets_created_user_id_foreign');
            $table->dropForeign('tasklist__timesheets_gruppo_id_foreign');
            $table->dropForeign('tasklist__timesheets_ordinativo_id_foreign');
            $table->dropForeign('tasklist__timesheets_procedura_id_foreign');
            $table->dropForeign('tasklist__timesheets_ticket_azione_id_foreign');
            $table->dropForeign('tasklist__timesheets_updated_user_id_foreign');
        });
    }
}
