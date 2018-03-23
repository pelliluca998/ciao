<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nome');
			$table->integer('id_oratorio')->unsigned()->index('id_oratorio');
			$table->integer('ordine')->unsigned();
			$table->string('note');
			$table->integer('id_type');
			$table->boolean('hidden');
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
		Schema::drop('attributos');
	}

}
