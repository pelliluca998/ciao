<?php
use Modules\User\Entities\Group;

Route::group(['middleware' => ['web', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Group\Http\Controllers'], function()
{
	Route::resource('group', 'GroupController', ['except' => ['edit', 'show']]);
	Route::get('group/getData', ['as' => 'group.data', 'uses' => 'GroupController@data']);
	Route::get('group/destroy', ['as' => 'group.destroy', 'uses' => 'GroupController@destroy']);
	Route::get('group/report_composer', ['as' => 'group.report_composer', 'uses' => 'GroupController@report_composer']);
	Route::get('group/edit', ['as' => 'group.edit', 'uses' => 'GroupController@edit']);
	Route::post('group/report_generator',['as' => 'group.report_generator', 'uses' => 'GroupController@report_generator']);
	Route::get('group/{id}/componenti', ['as' =>'group.componenti', 'uses' => 'GroupController@componenti']);

	Route::resource('groupusers', 'GroupUserController', ['only' => ['index', 'store']]);
//	Route::get('groupusers/show/{id_group}', ['as' => 'groupusers.showcomponents', 'uses' => 'GroupUserController@showcomponents']);
	Route::post('groupusers/store_user', ['as' => 'groupusers.store_user', 'uses' => 'GroupUserController@store_user']);
	Route::get('groupusers/{id_user}/destroy', 'GroupUserController@destroy');
	Route::get('groupusers/getData', ['as' =>'groupusers.data', 'uses' => 'GroupUserController@data']);
	Route::match(['get'], 'groupusers/select', ['as' => 'groupusers.select', 'uses' => 'GroupUserController@select']);
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
