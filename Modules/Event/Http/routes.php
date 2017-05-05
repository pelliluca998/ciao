<?php

Route::group(['middleware' => ['web', 'role:admin|owner', 'license:events'], 'prefix' => 'admin', 'namespace' => 'Modules\Event\Http\Controllers'], function()
{
	Route::resource('events', 'EventController', ['only' => ['index', 'update', 'create', 'store']]);
	Route::get('events/edit', ['as' => 'events.edit', 'uses' => 'EventController@edit']);
	Route::get('events/work', ['as' => 'events.work', 'uses' => 'EventController@work']);
	Route::get('events/destroy', ['as' => 'events.destroy', 'uses' => 'EventController@destroy']);
	Route::resource('week', 'WeekController', ['only' => ['index', 'update', 'create', 'store']]);
	Route::get('week/destroy', ['as' => 'week.destroy', 'uses' => 'WeekController@destroy']);
	Route::get('week/edit', ['as' => 'week.edit', 'uses' => 'WeekController@edit']);
	Route::get('week/showcampi/{id_week}', ['as' => 'week.showcampi', 'uses' => 'WeekController@show_campos_week']);
	Route::post('week/savecampos',['as' => 'week.savecampos', 'uses' => 'WeekController@savecampos']);
	Route::get('eventspecvalues/{id_sub}', ['as' => 'subscription.show_eventspecvalues', 'uses' => 'EventSpecController@show_eventspecvalues']);
});

Route::group(['middleware' => ['web', 'role:admin|owner', 'license:events'], 'prefix' => 'admin', 'namespace' => 'Modules\Event\Http\Controllers'], function()
{
	Route::get('eventspecs/show', ['as' => 'eventspecs.show', 'uses' => 'EventSpecController@show']);
	Route::post('eventspecs/save',['as' => 'eventspecs.save', 'uses' => 'EventSpecController@save']);
	Route::post('eventspecs/destroy', ['as' => 'eventspecs.destroy', 'uses' => 'EventSpecController@destroy']);
});

?>
