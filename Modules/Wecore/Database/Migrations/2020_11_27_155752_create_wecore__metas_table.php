<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWecoreMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wecore__metas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('value');
            $table->unsignedInteger('created_user_id')->index('wecore__metas_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('wecore__metas_updated_user_id_foreign');
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
        Schema::dropIfExists('wecore__metas');
    }
}
