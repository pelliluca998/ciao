<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateElencosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('elencos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('id_event')->unsigned()->index('id_event');
			$table->string('nome');
			$table->text('colonne', 65535);
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
		Schema::drop('elencos');
	}

}
