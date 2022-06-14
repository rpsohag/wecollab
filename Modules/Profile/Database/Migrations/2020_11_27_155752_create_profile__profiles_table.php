<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile__profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('profile__profiles_user_id_foreign');
            $table->string('titolo')->nullable();
            $table->string('matricola')->nullable();
            $table->string('badge')->nullable();
            $table->string('username');
            $table->string('incarico')->nullable();
            $table->dateTime('data_assunzione')->nullable();
            $table->dateTime('fine_contratto')->nullable();
            $table->string('titolo_di_studio')->nullable();
            $table->string('tipologia_di_contratto')->nullable();
            $table->string('codice_fiscale', 16)->nullable();
            $table->dateTime('data_di_nascita')->nullable();
            $table->string('comune_di_nascita')->nullable();
            $table->string('provincia_di_nascita')->nullable();
            $table->double('indennita_giornaliera')->nullable();
            $table->string('cognome_cedolino')->nullable();
            $table->string('interno')->nullable();
            $table->string('num_telefono_aziendale')->nullable();
            $table->string('num_telefono_personale')->nullable();
            $table->string('tipo_collaborazione', 255)->nullable();
            $table->smallInteger('avvisi_task')->nullable();
            $table->smallInteger('rendicontabile')->nullable();
            $table->text('partner')->nullable();
            $table->text('aree')->nullable();
            $table->string('azienda', 255)->nullable();
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
        Schema::dropIfExists('profile__profiles');
    }
}
