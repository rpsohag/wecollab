<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('profile__profiles', function (Blueprint $table) 
		{
            $table->json('approvatori_fpm')->nullable();
			$table->json('approvatori_rimborsi')->nullable();
			$table->json('visualizzatori')->nullable();
			$table->text('sede_partenza')->nullable();
			$table->double('indennita_pernottamento')->nullable();
			$table->double('ral')->nullable();
        });
    }
};
