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
        Schema::create('profile__autovetture', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->on('users');
			$table->text('targa');
			$table->text('modello');
			$table->double('costo_km')->default(0);
            $table->timestamps();
        });
    }
};
