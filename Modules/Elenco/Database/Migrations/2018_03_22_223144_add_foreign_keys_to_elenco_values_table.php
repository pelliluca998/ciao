<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToElencoValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('elenco_values', function(Blueprint $table)
		{
			$table->foreign('id_elenco', 'key_elenco_id')->references('id')->on('elencos')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('elenco_values', function(Blueprint $table)
		{
			$table->dropForeign('key_elenco_id');
		});
	}

}
