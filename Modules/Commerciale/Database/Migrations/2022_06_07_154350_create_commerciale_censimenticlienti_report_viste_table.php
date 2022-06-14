<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommercialeCensimenticlientiReportVisteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale_censimenticlienti_report_viste', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('data');
            $table->string('descrizione');
            $table->unsignedInteger('cliente_id');
            $table->unsignedInteger('created_user_id');
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
        Schema::dropIfExists('commerciale_censimenticlienti_report_viste');
    }
}
