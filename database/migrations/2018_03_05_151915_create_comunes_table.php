<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComunesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comuni', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_regione')->unsigned();
            $table->foreign('id_regione')->references('id')->on('regioni');
            $table->integer('id_provincia')->unsigned();
            $table->foreign('id_provincia')->references('id')->on('province');
            $table->text('nome');
            $table->boolean('capoluogo_provincia')->default(false);
            $table->string('codice_catastale', 255);
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
        Schema::dropIfExists('comunes');
    }
}
