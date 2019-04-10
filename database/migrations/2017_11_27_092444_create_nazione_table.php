<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNazioneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nazione', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome_stato');
            $table->string('sigla_numerica');
            $table->string('sigla_iso_3');
            $table->string('sigla_iso_2');
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
        Schema::dropIfExists('nazione');
    }
}
