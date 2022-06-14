<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('richieste__approvazioni', function (Blueprint $table) {
            $table->id();
			$table->foreignId('richiesta_id')->on('users__richieste');
			$table->foreignId('approvatore_id')->on('users');
			$table->integer('stato')->default(0);
            $table->timestamps();
        });
    }
};