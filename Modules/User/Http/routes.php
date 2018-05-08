<?php
use Modules\User\Entities\User;
Route::group(['middleware' => ['web', 'role:admin|owner', 'license:user'], 'prefix' => 'admin', 'namespace' => 'Modules\User\Http\Controllers'], function()
{
  Route::resource('user', 'UserController', ['except' => ['edit', 'show']]);
  Route::get('user/destroy', ['as' => 'user.destroy', 'uses' => 'UserController@destroy']);
  Route::get('user/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
  Route::get('user/print', ['as' => 'user.printprofile', 'uses' => 'UserController@print_userprofile']);
  Route::get('user/sistema_permessi', ['as' => 'user.permessi', 'uses' => 'UserController@sistema_permessi']);
  Route::get('user/getData', ['as' =>'user.data', 'uses' => 'UserController@data']);
  Route::post('user/action', ['as' =>'user.action', 'uses' => 'UserController@action']);
});

Route::group(['middleware' => ['web', 'license:user', 'role:user|admin|owner'], 'namespace' => 'Modules\User\Http\Controllers'], function() {
  Route::patch('user/updateprofile',['as' => 'user.updateprofile', 'uses' => 'UserController@updateprofile']);
  Route::get('profile/show', ['as' => 'profile.show', 'uses' => 'UserController@profile']);

  Route::get('user/dropdown', function(){
    $id_oratorio = Session::get('session_oratorio');
    return User::select('users.id', 'users.name', 'users.cognome')->leftJoin('user_oratorio', 'user_oratorio.id_user', '=', 'users.id')->where('user_oratorio.id_oratorio', $id_oratorio)->orderBy("users.cognome", "ASC")->get();
  });
});
