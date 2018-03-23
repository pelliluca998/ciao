<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToElencosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('elencos', function(Blueprint $table)
		{
			$table->foreign('id_event', 'elenco_fk0')->references('id')->on('events')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('elencos', function(Blueprint $table)
		{
			$table->dropForeign('elenco_fk0');
		});
	}

}
