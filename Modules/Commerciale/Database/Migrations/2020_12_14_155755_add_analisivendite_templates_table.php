<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnalisivenditeTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerciale__analisivendite_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->text('nome');
            $table->longtext('attivita');
            $table->unsignedInteger('created_user_id')->index('commerciale__analisivendite_templates_created_user_id_foreign');
            $table->unsignedInteger('updated_user_id')->index('commerciale__analisivendite_templates_updated_user_id_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commerciale__analisivendite_templates');
    }
}
