<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercialeOfferteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__offerte', function (Blueprint $table) {
            $table->increments('id');
            $table->string('azienda', 255)->nullable();
            $table->unsignedInteger('anno');
            $table->unsignedInteger('numero');
            $table->tinyInteger('stato')->nullable();
            $table->string('oggetto', 255);
            $table->dateTime('data_offerta');
            $table->float('importo_esente', 10, 0);
            $table->float('importo_iva', 10, 0)->nullable();
            $table->float('iva', 10);
            $table->text('note')->nullable();
            $table->tinyInteger('fatturata')->nullable()->default(0);
            $table->tinyInteger('offerta_pa')->nullable()->default(0);
            $table->tinyInteger('offerta_non_standard')->nullable()->default(0);
            $table->text('approvazioni')->nullable();
            $table->unsignedInteger('cliente_id')->index('commerciale__offerte_cliente_id_foreign');
            $table->unsignedInteger('offerta_definitiva_id')->nullable();
            $table->text('oda_determina_ids')->nullable();
            $table->unsignedInteger('ordine_mepa_id')->nullable();
            $table->unsignedInteger('created_user_id')->index('commerciale__offerte_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('commerciale__offerte_updated_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commerciale__offerte');
    }
}
