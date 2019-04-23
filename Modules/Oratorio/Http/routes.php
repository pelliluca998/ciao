<?php

Route::group(['middleware' => ['web',  'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Oratorio\Http\Controllers'], function()
{
	Route::resource('oratorio', 'OratorioController', ['only' => ['update', 'store']]);
	Route::resource('type', 'TypeController', ['only' => ['index', 'store']]);
	Route::resource('typeselect', 'TypeSelectController', ['only' => ['index', 'store']]);
	Route::get('type/getData', ['as' => 'type.data', 'uses' => 'TypeController@data']);
	Route::get('type/{id}/opzioni', ['as' =>'type.opzioni', 'uses' => 'TypeController@opzioni']);
	Route::get('typeselect/getData', ['as' => 'typeselect.data', 'uses' => 'TypeSelectController@data']);
	Route::get('oratorio/view', ['as' => 'oratorio.index', 'uses' => 'OratorioController@show']);
});
