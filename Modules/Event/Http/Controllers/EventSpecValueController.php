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
use App\Bilancio;
use App\User;
use App\Cassa;
use App\ModoPagamento;
use App\TipoPagamento;
use App\License;
use Module;
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
					//delete riga da bilancio
					if(Module::find('contabilita')!=null){
						$bilancio = Bilancio::where('id_eventspecvalues', $id)->get();
						if(count($bilancio)>0){
							foreach($bilancio as $b){
								$b->delete();
							}
						}
					}
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
		//var_dump($valore);
		$subscription = Subscription::findOrFail($id_subscription[0]);
		$user = User::findOrFail($subscription->id_user);
		foreach($valore as $value) {
			$old_pagato = 0;
			if($id_eventspecvalue[$i]>0){
				//update
				$spec = EventSpecValue::findOrFail($id_eventspecvalue[$i]);
				$old_pagato = $spec->pagato;				
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
			//contabilita
			if(Module::find('contabilita')!=null && License::isValid('contabilita')){
				if($old_pagato==0 && $pagato[$i]==1){
					//pagamento avvenuto ora, salvo una riga in bilancio
					$id_cassa=0;
					$id_modo=0;
					$id_tipo=0;
					$event_spec = EventSpec::where('id', $spec->id_eventspec)->first();	
					if($event_spec->id_cassa!=null){
						$id_cassa = $event_spec->id_cassa;
					}
					if($event_spec->id_modopagamento!=null){
						$id_modo = $event_spec->id_modopagamento;
					}
					if($event_spec->id_tipopagamento!=null){
						$id_tipo = $event_spec->id_tipopagamento;
					}					
					$bilancio = new Bilancio;
					$bilancio->id_event = Session::get('work_event');
					$bilancio->id_user = Auth::user()->id;
					$bilancio->id_eventspecvalues = $spec->id;
					$bilancio->id_tipopagamento = $id_tipo;
					$bilancio->id_modalita = $id_modo;
					$bilancio->id_cassa = $id_cassa;
					$bilancio->descrizione = "Pagamento da ".$user->cognome." ".$user->name." (iscrizione #".$subscription->id.")";
					$bilancio->importo = floatval($costo[$i]);
					$bilancio->data = date('Y-m-d');
					$bilancio->save();
				}elseif($old_pagato==1 && $pagato[$i]==0){
					//elimino la riga corrispondente in bilancio
					$bilancio = Bilancio::where('id_eventspecvalues', $spec->id)->get();
					if(count($bilancio)>0){
						foreach($bilancio as $b){
							$b->delete();
						}
					}
				}
			}
			//endcontabilita
    			$i++;
		}
		Session::flash("flash_message", "Dettagli salvati!");
       	$query = Session::get('query_param');
		Session::forget('query_param');
		if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner')){
			return redirect()->route('subscription.index', $query);
		}else{
			return redirect()->route('usersubscriptions.show');
		}
	}
}
