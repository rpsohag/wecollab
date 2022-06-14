<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTranslationTranslationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translation__translation_translations', function (Blueprint $table) {
            $table->foreign('translation_id')->references('id')->on('translation__translations')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translation__translation_translations', function (Blueprint $table) {
            $table->dropForeign('translation__translation_translations_translation_id_foreign');
        });
    }
}
