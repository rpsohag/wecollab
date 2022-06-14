<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistRinnovoNotificheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist__rinnovo_notifiche', function (Blueprint $table) {
            $table->increments('id');
            $table->string('notifica', 255);
            $table->unsignedInteger('cadenza');
            $table->unsignedInteger('tipo');
            $table->unsignedInteger('rinnovo_id')->index('tasklist__rinnovo_notifiche_rinnovo_id_foreign');
            $table->dateTime('sent_at')->nullable();
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
        Schema::dropIfExists('tasklist__rinnovo_notifiche');
    }
}
