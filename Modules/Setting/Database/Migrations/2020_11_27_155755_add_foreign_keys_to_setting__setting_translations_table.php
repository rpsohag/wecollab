<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSettingSettingTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting__setting_translations', function (Blueprint $table) {
            $table->foreign('setting_id')->references('id')->on('setting__settings')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting__setting_translations', function (Blueprint $table) {
            $table->dropForeign('setting__setting_translations_setting_id_foreign');
        });
    }
}
