<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('types', function(Blueprint $table)
		{
			$table->foreign('id_oratorio', 'types_fk0')->references('id')->on('oratorios')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('types', function(Blueprint $table)
		{
			$table->dropForeign('types_fk0');
		});
	}

}
