<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventSpecValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_spec_values', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_eventspec')->unsigned()->index('event_spec_values_id_eventspec_foreign');
			$table->integer('id_subscription')->unsigned()->index('id_subscription');
			$table->string('valore');
			$table->integer('id_week');
			$table->decimal('costo', 5)->default(0.00);
			$table->boolean('pagato')->default(0);
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
		Schema::drop('event_spec_values');
	}

}
