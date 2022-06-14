<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProfileGruppoUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile__gruppo_user', function (Blueprint $table) {
            $table->foreign('gruppo_id', 'profile__gruppi_users_gruppo_id_foreign')->references('id')->on('profile__gruppi')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id', 'profile__gruppi_users_user_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile__gruppo_user', function (Blueprint $table) {
            $table->dropForeign('profile__gruppi_users_gruppo_id_foreign');
            $table->dropForeign('profile__gruppi_users_user_id_foreign');
        });
    }
}
