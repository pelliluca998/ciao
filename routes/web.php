<?php
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Attributo\Entities\Attributo;
use Modules\User\Entities\Group;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', function(){
	return view('welcome');
});

Route::get('/informativa', function(){
	return view('informativa');
});

////////////////////////////////////////////////////////////
//////////////////ADMIN/////////////////////////////////
/////////////////////////////////////////////////////////

Route::group(['prefix' => 'admin', 'middleware' => ['role:admin', 'verified']], function() {
	Route::get('role', ['as' =>'role.index', 'uses' => 'RoleController@index']);
	Route::post('role/updatePermission', ['as' =>'role.updatePermission', 'uses' => 'RoleController@updatePermission']);
	Route::get('role/create', ['as' =>'role.create', 'uses' => 'RoleController@create']);
	Route::post('role/store', ['as' =>'role.store', 'uses' => 'RoleController@store']);
	Route::get('role/{id_role}/delete', ['as' =>'role.delete', 'uses' => 'RoleController@delete']);
});


////////////////////////////////////////////////
//////////////FINE ADMIN/////////////////////////
/////////////////////////////////////////////
Route::group(['middleware' => ['verified']], function() {
	Route::post('home/select_oratorio', ['as' => 'home.selectoratorio', 'uses' => 'HomeController@select_oratorio']);
	Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
	Route::get('/licenza', ['as' => 'licenza', 'uses' => 'HomeController@licenza']);
	Route::get('/admin', ['as' => 'admin', 'uses' => 'HomeController@admin']);

});


Route::get('eventspec/dropdown', function(){
	$id_week = Input::get('id_week');
	$id_event = Input::get('id_event');
	if($id_week==0){//specifica generale
		return EventSpec::where([["id_event", $id_event],['general', 1]])->orderBy("label")->get();
	}else{
		return EventSpec::where([["id_event", $id_event],['valid_for', 'LIKE', '%"'.$id_week.'":"1"%']])->orderBy("label")->get();
	}

});

Route::get('attributos/dropdown', function(){
	$id_oratorio = Input::get('id_oratorio');
	return Attributo::select('attributos.id', 'attributos.nome', 'attributos.id_type', 'types.label')->leftJoin('types', 'types.id', '=', 'attributos.id_type')->where([["attributos.id_oratorio", $id_oratorio], ["hidden", false]])->orderBy("ordine", "ASC")->get();
});

Route::get('attributos/type', function(){
	$id_attributo = Input::get('id_attributo');
	return Attributo::select('attributos.id_type', 'types.label')->leftJoin('types', 'types.id', '=', 'attributos.id_type')->where([["attributos.id", $id_attributo]])->orderBy("ordine", "ASC")->get();
});

Route::get('types/type', function(){
	$id_eventspec = Input::get('id_eventspec');
	return Type::select('event_specs.id_type as id', 'types.label')->rightJoin('event_specs', 'event_specs.id_type', '=', 'types.id')->where("event_specs.id", $id_eventspec)->get();
});

Route::get('types/type_attrib', function(){
	$id_attrib = Input::get('id_attrib');
	return Type::select('types.id', 'types.label')->leftJoin('attributos', 'attributos.id_type', '=', 'types.id')->where("attributos.id", $id_attrib)->get();
});

Route::get('types/options', function(){
	$id_type = Input::get('id_type');
	return TypeSelect::where("id_type", $id_type)->orderBy("ordine", "ASC")->get();
});

Route::get('comune/lista', ['as' =>'comune.lista', 'uses' => 'ComuneController@lista']);






Route::get('/whatsapp', ['as' => 'whatsapp', 'uses' => 'HomeController@whatsapp']);

?>
