<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserOratorioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_oratorio', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_user')->unsigned()->index('user_oratorio_id_user_foreign');
			$table->integer('id_oratorio')->unsigned()->index('user_oratorio_id_oratorio_foreign');
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
		Schema::drop('user_oratorio');
	}

}
