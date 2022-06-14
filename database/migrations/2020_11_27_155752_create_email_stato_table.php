<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailStatoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_stato', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('mail_id_inviate')->nullable();
            $table->bigInteger('mail_id_ricevute')->nullable();
            $table->tinyInteger('stato')->nullable();
            $table->tinyInteger('valida')->nullable();
            $table->index(['mail_id_inviate', 'mail_id_ricevute', 'stato'], 'ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_stato');
    }
}
