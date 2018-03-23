<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventSpecValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_spec_values', function(Blueprint $table)
		{
			$table->foreign('id_eventspec')->references('id')->on('event_specs')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('id_subscription', 'subscription_id')->references('id')->on('subscriptions')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_spec_values', function(Blueprint $table)
		{
			$table->dropForeign('event_spec_values_id_eventspec_foreign');
			$table->dropForeign('subscription_id');
		});
	}

}
