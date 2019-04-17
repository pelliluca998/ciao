<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAttributoUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attributo_users', function(Blueprint $table)
		{
			$table->foreign('id_attributo')->references('id')->on('attributos')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('id_user')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('attributo_users', function(Blueprint $table)
		{
			$table->dropForeign('attributo_users_id_attributo_foreign');
			$table->dropForeign('attributo_users_id_user_foreign');
		});
	}

}
