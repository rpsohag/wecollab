<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatisticheToAssistenzaRichiesteinterventiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assistenza__richiesteinterventi', function (Blueprint $table) {
            $table->json('statistiche')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assistenza__richiesteinterventi', function (Blueprint $table) {
            //
        });
    }
}
