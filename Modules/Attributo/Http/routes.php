<?php

Route::group(['middleware' => ['web', 'role:admin', 'license:attributo'], 'prefix' => 'admin', 'namespace' => 'Modules\Attributo\Http\Controllers'], function()
{
	Route::resource('attributouser', 'AttributoUserController', ['only' => ['index','update', 'store']]);
	Route::resource('attributo', 'AttributoController', ['only' => ['update', 'store']]);
	Route::get('attributo', ['as' => 'attributo.index', 'uses' => 'AttributoController@index']);
	Route::get('attributo/create', ['as' => 'attributo.create', 'uses' => 'AttributoController@create']);
	Route::get('attributo/edit', ['as' => 'attributo.edit', 'uses' => 'AttributoController@edit']);
	Route::get('attributo/destroy', ['as' => 'attributo.destroy', 'uses' => 'AttributoController@destroy']);
	Route::get('attributouser/show', ['as' => 'attributouser.show', 'uses' => 'AttributoUserController@show']);
	Route::get('attributouser/create', ['as' => 'attributouser.create', 'uses' => 'AttributoUserController@create']);
	Route::get('attributouser/edit', ['as' => 'attributouser.edit', 'uses' => 'AttributoUserController@edit']);
	Route::get('attributouser/destroy', ['as' => 'attributouser.destroy', 'uses' => 'AttributoUserController@destroy']);
});
