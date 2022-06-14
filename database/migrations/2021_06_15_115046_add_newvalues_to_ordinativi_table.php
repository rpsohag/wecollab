<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewvaluesToOrdinativiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__ordinativi', function (Blueprint $table) {
            $table->string('hash_link')->unique()->nullable();
            $table->integer('assistenza_per')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__ordinativi', function (Blueprint $table) {
            //
        });
    }
}
