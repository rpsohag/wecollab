<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWecloudFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wecloud__files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('value');
            $table->unsignedInteger('uploaded_user_id')->index('wecore__metas_uploaded_user_id_foreign');
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
        Schema::dropIfExists('wecloud__files');
    }
}
