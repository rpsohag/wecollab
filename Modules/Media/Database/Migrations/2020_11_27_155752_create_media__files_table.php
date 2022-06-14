<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media__files', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('is_folder')->default(0);
            $table->string('filename');
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('filesize')->nullable();
            $table->string('folder_id')->nullable();
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
        Schema::dropIfExists('media__files');
    }
}
