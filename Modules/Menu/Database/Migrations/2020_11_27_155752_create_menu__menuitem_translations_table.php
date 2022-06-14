<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuMenuitemTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu__menuitem_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('menuitem_id');
            $table->string('locale')->index();
            $table->tinyInteger('status')->default(0);
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('uri')->nullable();
            $table->timestamps();
            $table->unique(['menuitem_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu__menuitem_translations');
    }
}
