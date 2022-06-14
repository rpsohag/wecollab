<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailAllegatiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_allegati', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_mail');
            $table->text('file_path')->nullable();
            $table->tinyInteger('tipo')->nullable();
            $table->tinyInteger('azione')->nullable();
            $table->index(['id', 'id_mail'], 'ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_allegati');
    }
}
