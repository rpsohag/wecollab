<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWecoreMetagableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wecore__metagable', function (Blueprint $table) {
            $table->unsignedInteger('meta_id')->index('wecore__metagable_meta_id_foreign');
            $table->unsignedInteger('metagable_id');
            $table->string('metagable_type', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wecore__metagable');
    }
}
