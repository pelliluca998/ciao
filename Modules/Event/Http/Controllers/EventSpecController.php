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
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id){
		$sub = EventSpec::findOrFail($id);
		$event = Event::findOrFail($sub->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){		
			$sub->delete();
			Session::flash("flash_message", "Specifica $id cancellata!");
			return redirect()->route('events.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
    
	public function save(Request $request){
		$input = $request->all();
		$id_spec = Input::get('id_spec');
		$event = Input::get('event');
		$label = Input::get('label');
		$descrizione = Input::get('descrizione');
		$hidden = Input::get('hidden');
		$id_type = Input::get('id_type');
		$i=0;
		foreach($id_spec as $id) {
			if($id>0){
				//update
				$spec = EventSpec::findOrFail($id);
				$spec->label = $label[$i];
				$spec->descrizione = $descrizione[$i];
				$spec->id_type = $id_type[$i];
				$spec->hidden = $hidden[$i];
				$spec->general = $input['general'][$i];
				//Specifica valida per le settimane...
				if(isset($input['valid_for'][$id])){
					$weeks = $input['valid_for'][$id];
					$spec->valid_for = json_encode($weeks);
				}else{
					$spec->valid_for = json_encode(array());
				}			
				$spec->save();
			
			}else{
				$spec = new EventSpec;
				$spec->label = $label[$i];
				$spec->descrizione = $descrizione[$i];
				$spec->id_type = $id_type[$i];
				$spec->id_event = $event[$i];
				$spec->hidden = $hidden[$i];
				if(isset($input['valid_for'][$id])){
					$weeks = $input['valid_for'][$id];
					$spec->valid_for = json_encode($weeks);
				}else{
					$spec->valid_for = json_encode(array());
				}
				$spec->save();
			}
			$i++;
		}
		Session::flash("flash_message", "Specifiche evento aggiornate!");
		return redirect()->route('eventspecs.show');
	}
	
	public function show_eventspecvalues($id_sub){
		return view('subscription::eventspecvalue.show', ['id_sub' => $id_sub]);
	}
}
