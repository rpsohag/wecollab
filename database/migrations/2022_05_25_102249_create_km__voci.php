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
		Schema::create('km__voci', function (Blueprint $table) {
            $table->id();
			$table->foreignId('richiesta_id')->on('users__richieste');
			$table->timestamp('data')->nullable();
			$table->text('partenza');
			$table->text('arrivo');
			$table->integer('km');
			$table->integer('ar');
			$table->foreignId('attivita_id')->on('tasklist__attivita');
			$table->foreignId('ordinativo_id')->on('commerciale__ordinativi');
			$table->text('note')->nullable();
        	$table->timestamps();
        });
    }

};
