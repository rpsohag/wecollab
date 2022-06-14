<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingSettingTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting__setting_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('setting_id');
            $table->string('locale')->index();
            $table->string('value')->nullable();
            $table->text('description')->nullable();
            $table->unique(['setting_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting__setting_translations');
    }
}
