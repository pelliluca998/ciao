<?php

Route::group(['middleware' => ['web', 'verified'], 'prefix' => 'diocesi', 'namespace' => 'Modules\Diocesi\Http\Controllers'], function()
{
  Route::get('oratori', ['as' => 'oratori.index', 'uses' => 'DiocesiController@index_oratori']);
  Route::post('oratori', ['as' => 'oratori.store', 'uses' => 'DiocesiController@store_oratori']);
  Route::get('oratori/getData', ['as' => 'oratori.data', 'uses' => 'DiocesiController@data_oratori']);
});
