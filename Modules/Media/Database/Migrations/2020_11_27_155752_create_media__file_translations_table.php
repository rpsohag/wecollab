<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFileTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media__file_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('file_id');
            $table->string('locale')->index();
            $table->string('description')->nullable();
            $table->string('alt_attribute')->nullable();
            $table->string('keywords')->nullable();
            $table->unique(['file_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media__file_translations');
    }
}
