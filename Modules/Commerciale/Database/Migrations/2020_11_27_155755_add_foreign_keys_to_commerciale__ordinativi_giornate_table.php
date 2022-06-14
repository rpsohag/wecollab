<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommercialeOrdinativiGiornateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commerciale__ordinativi_giornate', function (Blueprint $table) {
            $table->foreign('gruppo_id')->references('id')->on('profile__gruppi')->onUpdate('RESTRICT')->onDelete('NO ACTION');
            $table->foreign('ordinativo_id')->references('id')->on('commerciale__ordinativi')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commerciale__ordinativi_giornate', function (Blueprint $table) {
            $table->dropForeign('commerciale__ordinativi_giornate_gruppo_id_foreign');
            $table->dropForeign('commerciale__ordinativi_giornate_ordinativo_id_foreign');
        });
    }
}
