<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributoUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributo_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_user')->unsigned()->index('attributo_users_id_user_foreign');
			$table->integer('id_attributo')->unsigned()->index('attributo_users_id_attributo_foreign');
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
		Schema::drop('attributo_users');
	}

}
