<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmministrazioneClientiReferentiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amministrazione__clienti_referenti', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 255);
            $table->string('cognome', 255);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('mansione', 255)->nullable();
            $table->unsignedInteger('cliente_id')->index('amministrazione__clienti_referenti_cliente_id_foreign');
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
        Schema::dropIfExists('amministrazione__clienti_referenti');
    }
}
