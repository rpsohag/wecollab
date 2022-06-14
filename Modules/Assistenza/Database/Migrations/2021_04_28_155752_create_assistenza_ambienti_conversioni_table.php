<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistenzaAmbientiConversioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('assistenza__ambienti_conversioni', function (Blueprint $table) {
            $table->increments('id');
            $table->text('nome')->nullable();
            $table->string('azienda', 255);
            //$table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->foreignId('cliente_id')->constrained('amministrazione__clienti')->nullable();
            $table->text('user_admin')->nullable();
            $table->text('password_admin')->nullable();
            $table->text('user_adm')->nullable();
            $table->text('password_adm')->nullable();
            $table->text('dettaglio_conversioni')->nullable();
            $table->boolean('chiuso')->default(false);
            //$table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('created_user_id')->foreignId('created_user_id')->constrained('users')->nullable();;
            //$table->unsignedBigInteger('updated_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->foreignId('updated_user_id')->constrained('users')->nullable();;
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
        Schema::dropIfExists('assistenza__ambienti_conversioni');
    }
}
