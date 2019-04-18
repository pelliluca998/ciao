<?php

Route::group(['middleware' => ['web', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Attributo\Http\Controllers'], function()
{
	Route::resource('attributouser', 'AttributoUserController', ['only' => ['index','update', 'store']]);
	Route::resource('attributo', 'AttributoController', ['only' => ['index', 'update', 'store']]);
	Route::get('attributo/getData', ['as' => 'attributo.data', 'uses' => 'AttributoController@data']);
	Route::get('attributouser/getData', ['as' => 'attributouser.data', 'uses' => 'AttributoUserController@data']);
	Route::post('attributo/valore_field', ['as' => 'attributo.valore_field', 'uses' => 'AttributoController@valore_field']);
});
