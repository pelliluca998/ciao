<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOratoriosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oratorios', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nome');
			$table->string('email');
			$table->string('logo')->default('');
			$table->string('sms_sender', 11)->default('');
			$table->boolean('reg_visible')->default(1);
			$table->string('reg_token');
			$table->timestamp('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('last_id_event')->default(0);
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
		Schema::drop('oratorios');
	}

}
