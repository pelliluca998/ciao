<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscriptions', function(Blueprint $table)
		{
			$table->foreign('id_event')->references('id')->on('events')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('id_user')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});

		Schema::table('event_spec_values', function(Blueprint $table)
		{
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
		Schema::table('subscriptions', function(Blueprint $table)
		{
			$table->dropForeign('subscriptions_id_event_foreign');
			$table->dropForeign('subscriptions_id_user_foreign');
		});
	}

}
