<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMenuMenuitemTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu__menuitem_translations', function (Blueprint $table) {
            $table->foreign('menuitem_id')->references('id')->on('menu__menuitems')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu__menuitem_translations', function (Blueprint $table) {
            $table->dropForeign('menu__menuitem_translations_menuitem_id_foreign');
        });
    }
}
