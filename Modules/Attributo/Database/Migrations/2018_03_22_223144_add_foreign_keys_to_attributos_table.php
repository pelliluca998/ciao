<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAttributosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attributos', function(Blueprint $table)
		{
			$table->foreign('id_oratorio', 'attributo_fk0')->references('id')->on('oratorios')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('attributos', function(Blueprint $table)
		{
			$table->dropForeign('attributo_fk0');
		});
	}

}
