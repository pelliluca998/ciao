<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('id_oratorio')->unsigned()->index('id_oratorio');
			$table->string('nome');
			$table->integer('anno');
			$table->text('descrizione');
			$table->boolean('active')->default(1);
			$table->string('firma')->default('Firma:');
			$table->string('image')->default('');
			$table->string('color')->default('');
			$table->boolean('more_subscriptions')->default(0)->comment('Indica se lo stesso utente può iscriversi più volte allo stesso evento');
			$table->boolean('stampa_anagrafica')->default(1);
			$table->integer('spec_iscrizione')->default(-1);
			$table->string('template_file')->nullable();
			$table->text('grazie', 65535);
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
		Schema::drop('events');
	}

}
