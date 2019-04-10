<?php

Route::group(['middleware' => ['web', 'license:event', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Event\Http\Controllers'], function()
{
	Route::resource('events', 'EventController', ['only' => ['index', 'update', 'create', 'store']]);
	Route::get('events/{id_event}/edit', ['as' => 'events.edit', 'uses' => 'EventController@edit']);
	Route::post('events/store_event', ['as' => 'events.store_event', 'uses' => 'EventController@store_event']);
	Route::get('events/{id_event}/clone', ['as' => 'events.clone', 'uses' => 'EventController@clone']);
	Route::get('events/{id_event}/work', ['as' => 'events.work', 'uses' => 'EventController@work']);
	// Route::get('events/destroy', ['as' => 'events.destroy', 'uses' => 'EventController@destroy']);
	Route::resource('week', 'WeekController', ['only' => ['index', 'update', 'create', 'store']]);
	// Route::get('week/showcampi/{id_week}', ['as' => 'week.showcampi', 'uses' => 'WeekController@show_campos_week']);
	// Route::post('week/savecampos',['as' => 'week.savecampos', 'uses' => 'WeekController@savecampos']);

	Route::get('events/strumenti', ['as' => 'events.strumenti', 'uses' => 'EventController@strumenti']);
	Route::get('events/getData', ['as' =>'events.data', 'uses' => 'EventController@data']);
	Route::get('week/getData', ['as' =>'week.data', 'uses' => 'WeekController@data']);

	Route::get('eventspecs/index', ['as' => 'eventspecs.index', 'uses' => 'EventSpecController@index']);
	Route::post('eventspecs/save',['as' => 'eventspecs.save', 'uses' => 'EventSpecController@save']);
	Route::post('eventspecs/destroy', ['as' => 'eventspecs.destroy', 'uses' => 'EventSpecController@destroy']);
	Route::post('eventspecs/riempi_specifica', ['as' => 'eventspecs.riempi_specifica', 'uses' => 'EventSpecValueController@riempi_specifica']);
	Route::post('eventspecs/elimina_specifica', ['as' => 'eventspecs.elimina_specifica', 'uses' => 'EventSpecValueController@elimina_specifica']);
	Route::post('eventspecs/aggiungi_specifica', ['as' => 'eventspecs.aggiungi_specifica', 'uses' => 'EventSpecValueController@aggiungi_specifica']);
});

Route::group(['middleware' => ['web', 'license:event', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Event\Http\Controllers'], function()
{
	Route::get('events/{id_event}/show', ['as' => 'events.show', 'uses' => 'EventController@show']);
});


?>
