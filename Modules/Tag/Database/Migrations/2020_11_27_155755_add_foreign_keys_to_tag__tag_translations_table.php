<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTagTagTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tag__tag_translations', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tag__tags')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tag__tag_translations', function (Blueprint $table) {
            $table->dropForeign('tag__tag_translations_tag_id_foreign');
        });
    }
}
