<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmministrazioneClientiAmbientiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amministrazione__clienti_ambienti', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('cliente_id')->index('amministrazione__clienti_indirizzi_cliente_id_foreign');
            $table->string('admin', 255)->nullable();
            $table->string('password_admin', 255)->nullable();
            $table->string('adm', 255)->nullable();
            $table->string('password_adm', 255)->nullable();
            $table->string('n_db', 255)->nullable();
            $table->string('ambiente', 255)->nullable();
            $table->tinyInteger('api_sso')->nullable()->default(0);
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
        Schema::dropIfExists('amministrazione__clienti_ambienti');
    }
}
