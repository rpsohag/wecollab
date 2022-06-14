<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPagePageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page__page_translations', function (Blueprint $table) {
            $table->foreign('page_id')->references('id')->on('page__pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page__page_translations', function (Blueprint $table) {
            $table->dropForeign('page__page_translations_page_id_foreign');
        });
    }
}
