<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailRicevuteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_ricevute', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->tinyInteger('casella_id')->nullable();
            $table->string('message_id', 255)->nullable();
            $table->bigInteger('mail_id')->nullable();
            $table->mediumText('oggetto')->nullable();
            $table->longText('bodytext')->nullable();
            $table->longText('bodyhtml')->nullable();
            $table->string('from', 255)->nullable();
            $table->text('to')->nullable();
            $table->dateTime('data')->nullable();
            $table->tinyInteger('tipologia')->nullable()->comment('1=email;2=pec;3=pec_accettata;4=pec_consegnata');
            $table->tinyInteger('verificata')->nullable();
            $table->tinyInteger('letta')->default(0);
            $table->index(['id', 'casella_id', 'message_id', 'mail_id', 'tipologia', 'verificata', 'data'], 'ids');
        });
        
        \DB::statement('ALTER TABLE `email_ricevute` ADD FULLTEXT `texts` (`oggetto`, `from`, `to`, `bodyhtml`, `bodytext`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_ricevute');
    }
}
