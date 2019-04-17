<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTypeSelectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('type_selects', function(Blueprint $table)
		{
			$table->foreign('id_type')->references('id')->on('types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('type_selects', function(Blueprint $table)
		{
			$table->dropForeign('type_selects_id_type_foreign');
		});
	}

}
