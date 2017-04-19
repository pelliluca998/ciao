<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Requests;
use App\EventSpecValue;
use App\EventSpec;
use App\Event;
use App\Subscription;
use Input;
use Session;
use Auth;

class EventSpecValueController extends Controller
{
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
	}   
    

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(){
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request){   	
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show($id){
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id){
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update($id, Request $request){
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	public function destroy($id){
		$value = EventSpecValue::findOrFail($id);
		$eventSpec = EventSpec::findOrFail($value->id_eventspec);
		$event = Event::findOrFail($eventSpec->id_event);
		$subscription = Subscription::findOrFail($value->id_subscription);
		if($subscription->confirmed==1){
			Session::flash("flash_message", "Impossibile eliminare la specifica selezionata, l'iscrizione è già confermata!");
		}else{
			if(Auth::user()->hasRole('admin')){
				if($event->id_oratorio==Session::get('session_oratorio')){					
					$value->delete();
					Session::flash("flash_message", "Specifica $id cancellata!");
				}else{
					abort(403, 'Unauthorized action.');
				}
			}elseif(Auth::user()->hasRole('user')){
				if($subscription->id_user == Auth::user()->id){
					$value->delete();
					Session::flash("flash_message", "Specifica $id cancellata!");
				}else{
					abort(403, 'Unauthorized action.');
				}
			}
		}		
		
		$query = Session::get('query_param');
		Session::forget('query_param');
		if(Auth::user()->hasRole('admin')){
			return redirect()->route('subscription.index', $query);
		}else{
			return redirect()->route('usersubscriptions.show');
		}
		
	}
    

	public function save(Request $request){
		$valore = Input::get('valore');
		$costo = Input::get('costo');
		$pagato = Input::get('pagato');
		$id_eventspec = Input::get('id_eventspec');
		$id_eventspecvalue = Input::get('id_eventspecvalue');
		$id_subscription = Input::get('id_subscription');
		$id_week = Input::get('id_week');
		$i=0;
		ksort($valore);
		var_dump($valore);
		foreach($valore as $value) {
			if($id_eventspecvalue[$i]>0){
				//update
				$spec = EventSpecValue::findOrFail($id_eventspecvalue[$i]);
				$spec->valore = $value;
				$spec->costo = floatval($costo[$i]);
				$spec->pagato = $pagato[$i];
				$spec->save();
				
			}else{
				$spec = new EventSpecValue;
				$spec->id_eventspec = $id_eventspec[$i];
				$spec->valore = $value;
				$spec->id_subscription = $id_subscription[$i];
				$spec->id_week = $id_week[$i];
				$spec->costo = floatval($costo[$i]);
				$spec->pagato = $pagato[$i];
				$spec->save();
			}
    			$i++;
		}
		Session::flash("flash_message", "Dettagli salvati!");
       	$query = Session::get('query_param');
		Session::forget('query_param');
		if(Auth::user()->hasRole('admin')){
			return redirect()->route('subscription.index', $query);
		}else{
			return redirect()->route('usersubscriptions.show');
		}
	}
}
