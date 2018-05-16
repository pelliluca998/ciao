<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccontoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('event_specs', function($table) {
        $table->text('acconto')->nullable(true)->default(null)->after('price');
      });

      Schema::table('event_spec_values', function($table) {
        $table->decimal('acconto', 5, 2)->default(0)->after('costo');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
}
