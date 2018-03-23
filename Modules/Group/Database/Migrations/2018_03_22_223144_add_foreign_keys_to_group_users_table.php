<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_users', function(Blueprint $table)
		{
			$table->foreign('id_group')->references('id')->on('groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::table('group_users', function(Blueprint $table)
		{
			$table->dropForeign('group_users_id_group_foreign');
			$table->dropForeign('group_users_id_user_foreign');
		});
	}

}
