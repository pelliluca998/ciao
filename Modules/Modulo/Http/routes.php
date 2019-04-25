<?php

Route::group(['middleware' => ['web', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Modulo\Http\Controllers'], function()
{
    Route::resource('modulo', 'ModuloController', ['only' => ['store', 'index']]);
    Route::get('modulo/getData', ['as' =>'modulo.data', 'uses' => 'ModuloController@data']);
    Route::get('modulo/{id_modulo}/download', ['as' =>'modulo.download', 'uses' => 'ModuloController@download']);
});
