<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Requests;
use App\EventSpec;
use App\Event;
use Auth;
use Input;
use Session;

class EventSpecController extends Controller
{
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
		//return view('groups.show');
	}   
    

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	//public function create(){
	//	return view('groups.create');
	//}

	

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show(Request $request){
        	$input = $request->all();
        	if(isset($input['id_event'])){
        		$id = $input['id_event'];
        	}elseif(Session::has('work_event')){
        		$id = Session::get('work_event');
        	}else{
        		Session::flash("flash_message", "Devi specificare un evento per compiere questa azione!");
			return redirect()->route('events.index');
        	}
		
		$event = Event::findOrFail($id);

		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('event::eventspecs.show')->with('id_event', $id);
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}

	

	/**
	* Remove the specified resource from storage.
	* Funzione chiamata da jquery in app
	* @param  int  $id
	* @return Response
	*/
	public function destroy(Request $request){
		$input = $request->all();
		$id = $input['id_spec'];
		$sub = EventSpec::findOrFail($id);
		$event = Event::findOrFail($sub->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){		
			$sub->delete();
			//Session::flash("flash_message", "Specifica $id cancellata!");
			//return redirect()->route('eventspecs.show', ['id_event' => $sub->id_event]);
			echo true;
		}else{
			//abort(403, 'Unauthorized action.');
			echo false;
		}
	}
    
	public function save(Request $request){
		$input = $request->all();
		$id_spec = Input::get('id_spec');
		$event = Input::get('event');
		$label = Input::get('label');
		$descrizione = Input::get('descrizione');
		$hidden = Input::get('hidden');
		$ordine = Input::get('ordine');
		$id_type = Input::get('id_type');
		$id_cassa = Input::get('cassa');
		$id_modo_pagamento = Input::get('modo_pagamento');
		$id_tipo_pagamento = Input::get('tipo_pagamento');
		$keys = array_keys($id_spec);
		foreach($keys as $key) {
			if($id_spec[$key]>0){
				//update
				$spec = EventSpec::findOrFail($id_spec[$key]);
				$spec->label = $label[$key];
				$spec->descrizione = $descrizione[$key];
				$spec->id_type = $id_type[$key];
				$spec->hidden = $hidden[$key];
				$spec->ordine = $ordine[$key];
				$spec->id_cassa = $id_cassa[$key];
				$spec->id_modopagamento = $id_modo_pagamento[$key];
				$spec->id_tipopagamento = $id_tipo_pagamento[$key];
				$spec->general = $input['general'][$key];
				//Specifica valida per le settimane...
				if(isset($input['valid_for'][$id_spec[$key]])){
					$weeks = $input['valid_for'][$id_spec[$key]];
					$spec->valid_for = json_encode($weeks);
				}else{
					$spec->valid_for = json_encode(array());
				}
				$spec->price = json_encode($input['price'][$id_spec[$key]]);
				$spec->save();
		
			}else{
				$spec = new EventSpec;
				$spec->label = $label[$key];
				$spec->descrizione = $descrizione[$key];
				$spec->id_type = $id_type[$key];
				$spec->id_event = $event[$key];
				$spec->hidden = $hidden[$key];
				$spec->ordine = $ordine[$key];
				$spec->id_cassa = null;
				$spec->id_modopagamento = null;
				$spec->id_tipopagamento = null;
				if(isset($input['valid_for'][$id_spec[$key]])){
					$weeks = $input['valid_for'][$id_spec[$key]];
					$spec->valid_for = json_encode($weeks);
				}else{
					$spec->valid_for = json_encode(array());
				}
				$spec->price = json_encode(array());
				$spec->save();
			}
		}
		Session::flash("flash_message", "Specifiche evento aggiornate!");
		return redirect()->route('eventspecs.show');
	}
	
	public function show_eventspecvalues($id_sub){
		return view('subscription::eventspecvalue.show', ['id_sub' => $id_sub]);
	}
}
