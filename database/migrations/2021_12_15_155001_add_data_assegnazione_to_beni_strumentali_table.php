<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataAssegnazioneToBeniStrumentaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beni_strumentali', function (Blueprint $table) {
            $table->timestamp('data_assegnazione')->nullable()->default(date('Y-m-d'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beni_strumentali', function (Blueprint $table) {
            //
        });
    }
}
