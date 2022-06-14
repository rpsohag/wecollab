<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__timesheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->unsignedInteger('cliente_id')->index('tasklist__timesheets_cliente_id_foreign');
            $table->unsignedInteger('procedura_id')->index('tasklist__timesheets_procedura_id_foreign');
            $table->unsignedInteger('area_id')->index('tasklist__timesheets_area_id_foreign');
            $table->unsignedInteger('gruppo_id')->index('tasklist__timesheets_gruppo_id_foreign');
            $table->unsignedInteger('ordinativo_id')->nullable()->index('tasklist__timesheets_ordinativo_id_foreign');
            $table->unsignedInteger('attivita_id')->nullable()->index('tasklist__timesheets_attivita_id_foreign');
            $table->unsignedInteger('ticket_azione_id')->nullable()->index('tasklist__timesheets_ticket_azione_id_foreign');
            $table->dateTime('dataora_inizio');
            $table->dateTime('dataora_fine');
            $table->text('nota')->nullable();
            $table->unsignedInteger('created_user_id')->index('tasklist__timesheets_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('tasklist__timesheets_updated_user_id_foreign');
            $table->timestamps();
            $table->integer('tipologia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasklist__timesheets');
    }
}
