<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeOrdinativiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__ordinativi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda');
            $table->unsignedInteger('offerta_id')->index('commerciale__ordinativi_offerta_id_foreign');
            $table->dateTime('data_inizio');
            $table->text('oggetto');
            $table->dateTime('data_fine')->nullable();
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
        Schema::dropIfExists('commerciale__ordinativi');
    }
}
