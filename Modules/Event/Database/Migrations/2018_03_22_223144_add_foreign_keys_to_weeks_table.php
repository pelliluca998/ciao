<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToWeeksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('weeks', function(Blueprint $table)
		{
			$table->foreign('id_event')->references('id')->on('events')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('weeks', function(Blueprint $table)
		{
			$table->dropForeign('weeks_id_event_foreign');
		});
	}

}
