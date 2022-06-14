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
        Schema::create('trasferte__voci', function (Blueprint $table) {
            $table->id();
			$table->foreignId('richiesta_id')->on('users__richieste');
			$table->timestamp('data')->nullable();
			$table->integer('tipologia');
			$table->double('importo')->default(0);
			$table->foreignId('attivita_id')->on('tasklist__attivita');
			$table->foreignId('ordinativo_id')->on('commerciale__ordinativi');
			$table->text('note')->nullable();
        	$table->timestamps();
        });
    }

};
