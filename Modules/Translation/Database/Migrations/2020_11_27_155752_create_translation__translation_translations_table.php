<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationTranslationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation__translation_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('value');
            $table->unsignedInteger('translation_id');
            $table->string('locale')->index();
            $table->unique(['translation_id', 'locale'], 'translations_trans_id_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation__translation_translations');
    }
}
