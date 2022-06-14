<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMediaFileTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media__file_translations', function (Blueprint $table) {
            $table->foreign('file_id')->references('id')->on('media__files')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media__file_translations', function (Blueprint $table) {
            $table->dropForeign('media__file_translations_file_id_foreign');
        });
    }
}
