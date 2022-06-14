<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistRinnoviTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__rinnovi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255);
            $table->string('titolo', 255);
            $table->text('descrizione')->nullable();
            $table->dateTime('data');
            $table->unsignedInteger('tipo');
            $table->unsignedInteger('ordinativo_id')->nullable();
            $table->unsignedInteger('cliente_id')->nullable()->index('tasklist__rinnovi_cliente_id_foreign');
            $table->unsignedInteger('created_user_id')->nullable()->index('tasklist__rinnovi_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->nullable()->index('tasklist__rinnovi_updated_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasklist__rinnovi');
    }
}
