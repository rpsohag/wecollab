<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeAnalisivenditeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__analisivendite', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255)->index('azienda');
            $table->string('titolo', 255);
            $table->dateTime('data');
            $table->longText('attivita');
            $table->text('canoni')->nullable();
            $table->text('costi_fissi')->nullable();
            $table->integer('censimento_id')->index('censimento_id');
            $table->integer('offerta_id')->nullable()->index('offerta_id');
            $table->integer('commerciale_id');
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
        Schema::dropIfExists('commerciale__analisivendite');
    }
}
