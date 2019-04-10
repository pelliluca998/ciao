<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParrocchiaToOratoriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oratorios', function (Blueprint $table) {
          $table->string('nome_parrocchia')->nullable()->after('nome');
          $table->string('indirizzo_parrocchia')->nullable()->after('nome_parrocchia');
          $table->string('nome_diocesi')->nullable()->after('indirizzo_parrocchia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oratorios', function (Blueprint $table) {

        });
    }
}
