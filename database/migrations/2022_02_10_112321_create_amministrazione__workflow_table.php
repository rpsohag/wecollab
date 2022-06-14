<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmministrazioneWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amministrazione__workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->boolean('nodo_finale')->default(false);
            $table->foreignId('parent_id')->on('users');
            $table->integer('type')->default(1);
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
        Schema::dropIfExists('amministrazione__workflow');
    }
}
