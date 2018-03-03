<?php
use Modules\User\Entities\Group;

Route::group(['middleware' => ['web', 'role:admin|owner', 'license:group'], 'prefix' => 'admin', 'namespace' => 'Modules\Group\Http\Controllers'], function()
{
	Route::resource('group', 'GroupController', ['except' => ['edit', 'show']]);
	Route::resource('groupuser', 'GroupUserController', ['only' => ['store']]);
	Route::get('group/destroy', ['as' => 'group.destroy', 'uses' => 'GroupController@destroy']);
	Route::get('group/report_composer', ['as' => 'group.report_composer', 'uses' => 'GroupController@report_composer']);
	Route::get('group/edit', ['as' => 'group.edit', 'uses' => 'GroupController@edit']);
	Route::post('group/report_generator',['as' => 'group.report_generator', 'uses' => 'GroupController@report_generator']);
	
	Route::get('groupusers/show/{id_group}', ['as' => 'groupusers.showcomponents', 'uses' => 'GroupUserController@showcomponents']);
	Route::get('groupusers/{id_user}/destroy', 'GroupUserController@destroy');
	Route::post('groupusers/select', ['as' => 'groupusers.select', 'uses' => 'GroupUserController@select']);
	//Route::post('groupusers/create', ['as' => 'groupusers.new', 'uses' => 'GroupUserController@create']);
	Route::get('groups/dropdown', function(){
	    	return Group::where("id_oratorio", Session::get('session_oratorio'))->orderBy("nome", "ASC")->get();
	});
});


Route::group(['namespace' => 'Modules\Group\Http\Controllers'], function()
{
	Route::get('groups/dropdown', function(){
		$id_oratorio = Input::get('id_oratorio');
	    	return Group::where("id_oratorio", $id_oratorio)->orderBy("nome", "ASC")->get();
	});

});

