<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTypeSelectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('type_selects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_type')->unsigned()->index('type_selects_id_type_foreign');
			$table->string('option');
			$table->integer('ordine')->default(0);
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
		Schema::drop('type_selects');
	}

}
