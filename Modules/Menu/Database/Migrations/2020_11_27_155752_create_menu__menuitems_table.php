<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuMenuitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu__menuitems', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('menu_id')->index('menu__menuitems_menu_id_foreign');
            $table->unsignedInteger('page_id')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('target', 10)->nullable();
            $table->string('link_type')->default('page');
            $table->string('class')->nullable()->default('');
            $table->string('module_name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('lft')->nullable();
            $table->integer('rgt')->nullable();
            $table->integer('depth')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_root')->default(0);
            $table->string('icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu__menuitems');
    }
}
