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
        Schema::create('users__richieste', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->on('users');
			$table->integer('tipologia');
			$table->integer('stato')->default(0);
			$table->text('meta')->nullable();
			$table->timestamp('from')->nullable();
			$table->timestamp('to')->nullable();
			$table->text('note')->nullable();
			$table->integer('mese')->nullable();
			$table->integer('anno')->nullable();
			$table->text('targa')->nullable();
			$table->text('modello')->nullable();
			$table->double('costo_km')->default(0);
			$table->double('totale')->default(0);
			$table->integer('draft')->default(0);
            $table->timestamps();
        });
    }
};
