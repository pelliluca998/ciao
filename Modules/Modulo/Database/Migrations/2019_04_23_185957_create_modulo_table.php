<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('path_file');
            $table->integer('id_oratorio')->unsigned();
            $table->foreign('id_oratorio')->references('id')->on('oratorios')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::dropIfExists('modulo');
    }
}
