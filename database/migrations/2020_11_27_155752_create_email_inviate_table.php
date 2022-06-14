<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailInviateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_inviate', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('message_id', 255)->nullable();
            $table->text('oggetto')->nullable();
            $table->longText('bodyhtml')->nullable();
            $table->longText('bodytext')->nullable();
            $table->string('to', 255)->nullable();
            $table->text('from')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->index(['message_id', 'id'], 'ids');
        });

        \DB::statement('ALTER TABLE `email_inviate` ADD FULLTEXT `texts` (`oggetto`, `bodyhtml`, `from`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_inviate');
    }
}
