<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProfileAreaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile__area_user', function (Blueprint $table) {
            $table->foreign('area_id', 'profile__area_user_ibfk_1')->references('id')->on('profile__aree')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'profile__area_user_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile__area_user', function (Blueprint $table) {
            $table->dropForeign('profile__area_user_ibfk_1');
            $table->dropForeign('profile__area_user_ibfk_2');
        });
    }
}
