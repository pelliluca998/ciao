<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvinciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('province', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_regione')->unsigned();
            $table->foreign('id_regione')->references('id')->on('regioni');
            $table->integer('codice_citta_metropolitana')->nullable(true);
            $table->text('nome');
            $table->string('sigla_automobilistica', 2);
            $table->decimal('latitudine', 9,6);
            $table->decimal('longitudine', 9,6);
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
        Schema::dropIfExists('provincias');
    }
}
