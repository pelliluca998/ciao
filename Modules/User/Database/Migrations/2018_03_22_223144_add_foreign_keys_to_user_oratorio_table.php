<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserOratorioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_oratorio', function(Blueprint $table)
		{
			$table->foreign('id_user', 'user_oratorio_fk0')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('id_oratorio', 'user_oratorio_fk1')->references('id')->on('oratorios')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_oratorio', function(Blueprint $table)
		{
			$table->dropForeign('user_oratorio_fk0');
			$table->dropForeign('user_oratorio_fk1');
		});
	}

}
