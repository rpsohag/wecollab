<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWecoreMetagableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wecore__metagable', function (Blueprint $table) {
            $table->foreign('meta_id')->references('id')->on('wecore__metas')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wecore__metagable', function (Blueprint $table) {
            $table->dropForeign('wecore__metagable_meta_id_foreign');
        });
    }
}
