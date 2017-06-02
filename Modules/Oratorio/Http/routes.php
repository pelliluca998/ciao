<?php

Route::group(['middleware' => ['web', 'role:admin|owner', 'license:oratorio'], 'prefix' => 'admin', 'namespace' => 'Modules\Oratorio\Http\Controllers'], function()
{
	Route::resource('oratorio', 'OratorioController', ['only' => ['update', 'store']]);
	Route::resource('type', 'TypeController', ['only' => ['index', 'update', 'create', 'store']]);
	Route::get('oratorio/view', ['as' => 'oratorio.index', 'uses' => 'OratorioController@show']);
	Route::post('typeselect/save',['as' => 'typeselect.save', 'uses' => 'TypeSelectController@save']);
	Route::get('type/destroy', ['as' => 'type.destroy', 'uses' => 'TypeController@destroy']);
	Route::get('type/edit', ['as' => 'type.edit', 'uses' => 'TypeController@edit']);
	
	Route::get('typeselect/show/{id_type}', ['as' => 'typeselect.show', 'uses' => 'TypeSelectController@show']);
	Route::get('typeselect/{id_typeselects}/destroy', ['as' => 'typeselect.destroy', 'uses' => 'TypeSelectController@destroy']);
});

Route::group(['middleware' => ['web', 'role:owner', 'license:oratorio'], 'prefix' => 'owner', 'namespace' => 'Modules\Oratorio\Http\Controllers'], function()
{
	Route::get('oratorio/showall', ['as' => 'oratorio.showall', 'uses' => 'OratorioController@showall']);
	Route::get('oratorio/destroy', ['as' => 'oratorio.destroy', 'uses' => 'OratorioController@destroy']);
	Route::get('oratorio/edit', ['as' => 'oratorioowner.edit', 'uses' => 'OratorioController@edit_owner']);
	Route::get('oratorioowner/work', ['as' => 'oratorioowner.work', 'uses' => 'OratorioController@work']);
	Route::post('oratorio/save_message', ['as' => 'oratorio.save_message', 'uses' => 'OratorioController@save_message']);
	Route::get('oratorio/new_message', ['as' => 'oratorio.new_message', 'uses' => 'OratorioController@new_message']);
	Route::patch('oratorio/update', ['as' => 'oratorioowner.update', 'uses' => 'OratorioController@update_owner']);
	Route::get('oratorio/create', ['as' => 'oratorio.create', 'uses' => 'OratorioController@create']);

});

Route::group(['middleware' => ['web'], 'namespace' => 'Modules\Oratorio\Http\Controllers'], function()
{
	Route::post('/neworatorio', ['as' => 'oratorio.neworatorio', 'uses' => 'OratorioController@neworatorio']);
	Route::post('/neworatorio_emailfromuser', ['as' => 'oratorio.neworatorio_emailfromuser', 'uses' => 'OratorioController@neworatorio_emailfromuser']);
	Route::get('/affiliazione', ['as' => 'oratorio.affiliazione', 'uses' => 'OratorioController@affiliazione']);
	Route::post('/salva_affiliazione', ['as' => 'oratorio.salva_affiliazione', 'uses' => 'OratorioController@salva_affiliazione']);
	Route::post('/elimina_affiliazione', ['as' => 'oratorio.elimina_affiliazione', 'uses' => 'OratorioController@elimina_affiliazione']);

});
