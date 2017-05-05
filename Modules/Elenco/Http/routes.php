<?php

Route::group(['middleware' => ['web', 'role:admin|owner', 'license:elenco'], 'prefix' => 'admin', 'namespace' => 'Modules\Elenco\Http\Controllers'], function()
{
	Route::resource('elenco', 'ElencoController', ['only' => ['index', 'update', 'create', 'store']]);
	Route::get('elenco/edit', ['as' => 'elenco.edit', 'uses' => 'ElencoController@edit']);
	Route::get('elenco/show_riempi', ['as' => 'elenco.show_riempi', 'uses' => 'ElencoController@show_riempi']);
	Route::get('elenco/destroy', ['as' => 'elenco.destroy', 'uses' => 'ElencoController@destroy']);
	Route::get('elenco/show', ['as' => 'elenco.show', 'uses' => 'ElencoController@show']);
	Route::get('elenco/print', ['as' => 'elenco.print', 'uses' => 'ElencoController@print']);
	Route::post('elenco/report',['as' => 'elenco.report', 'uses' => 'ElencoController@report']);
	Route::post('elenco/riempi',['as' => 'elenco.riempi', 'uses' => 'ElencoController@riempi']);
	Route::post('elenco/save_values',['as' => 'elenco.save_values', 'uses' => 'ElencoController@save_values']);
	Route::post('elenco/destroy_value', ['as' => 'elenco.destroy_value', 'uses' => 'ElencoController@destroy_value']);
});
