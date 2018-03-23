<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateElencoValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('elenco_values', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('id_user');
			$table->integer('id_elenco')->index('id_elenco');
			$table->string('valore');
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
		Schema::drop('elenco_values');
	}

}
