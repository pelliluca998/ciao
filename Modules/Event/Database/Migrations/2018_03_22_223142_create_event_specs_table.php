<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventSpecsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_specs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_event')->unsigned()->index('id_event');
			$table->integer('ordine')->default(0);
			$table->boolean('general')->default(1);
			$table->string('valid_for');
			$table->string('label');
			$table->string('descrizione');
			$table->boolean('hidden')->default(0);
			$table->integer('id_type');
			$table->text('price', 65535);
			$table->integer('id_cassa')->nullable();
			$table->integer('id_tipopagamento')->nullable();
			$table->integer('id_modopagamento')->nullable();
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
		Schema::drop('event_specs');
	}

}
