<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComuneProvinciaToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->integer('id_nazione_nascita')->unsigned()->before('created_at')->nullable()->default(118);
          $table->foreign('id_nazione_nascita')->references('id')->on('nazione');
          $table->integer('id_comune_nascita')->unsigned()->before('created_at')->nullable();
          $table->foreign('id_comune_nascita')->references('id')->on('comuni');
          $table->integer('id_provincia_nascita')->nullable()->before('created_at')->unsigned();
          $table->foreign('id_provincia_nascita')->references('id')->on('province')->onDelete('set null');
          $table->integer('id_comune_residenza')->unsigned()->before('created_at')->nullable();
          $table->foreign('id_comune_residenza')->references('id')->on('comuni');
          $table->integer('id_provincia_residenza')->nullable()->before('created_at')->unsigned();
          $table->foreign('id_provincia_residenza')->references('id')->on('province')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

        });
    }
}
