<?php
Route::group(['middleware' => ['web', 'role:admin', 'license:user'], 'prefix' => 'admin', 'namespace' => 'Modules\User\Http\Controllers'], function()
{
    Route::resource('user', 'UserController', ['except' => ['edit', 'show']]);
    Route::get('user/destroy', ['as' => 'user.destroy', 'uses' => 'UserController@destroy']);
	Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
	Route::get('user/print', ['as' => 'user.printprofile', 'uses' => 'UserController@print_userprofile']);
});

Route::group(['middleware' => ['web', 'license:user', 'role:user|admin'], 'namespace' => 'Modules\User\Http\Controllers'], function() {
    Route::patch('user/updateprofile',['as' => 'user.updateprofile', 'uses' => 'UserController@updateprofile']);
    Route::get('profile/show', ['as' => 'profile.show', 'uses' => 'UserController@profile']);
});
