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

////////////////////////////////////////////////////////////
//////////////////ADMIN/////////////////////////////////
/////////////////////////////////////////////////////////

Route::group(['prefix' => 'admin', 'middleware' => ['license:', 'role:admin']], function() {
	//Route::get('/', 'HomeController@admin');
	//Route::get('emails/subscription', 'EmailController@send_at_subscribers');
	//Route::get('/specsubscriptions/view','SpecSubscriptionController@show');

	Route::get('campoweeks/{id_campo}/destroy', 'WeekController@destroy_campo');


});


////////////////////////////////////////////////
//////////////FINE ADMIN/////////////////////////
/////////////////////////////////////////////
Route::group(['middleware' => ['role:user|admin|owner']], function() {
	Route::post('home/select_oratorio', ['as' => 'home.selectoratorio', 'uses' => 'HomeController@select_oratorio']);
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


Auth::routes();

Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('/licenza', ['as' => 'licenza', 'uses' => 'HomeController@licenza']);
Route::get('/admin', ['as' => 'admin', 'uses' => 'HomeController@admin']);
Route::get('/', function(){
	return view('welcome');
});

Route::get('/whatsapp', ['as' => 'whatsapp', 'uses' => 'HomeController@whatsapp']);

?>
