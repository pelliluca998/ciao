<?php

namespace Modules\Elenco\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Elenco\Entities\Elenco;
use Modules\Elenco\Entities\ElencoValue;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpecValue;
use Modules\Subscription\Entities\Subscription;
use Session;

class ElencoController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
	public function index(Request $request){
		if(!isset($request['id_event']) && !Session::has('work_event')){
			Session::flash('flash_message', 'Per vedere le iscrizioni, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}
		
		if(Session::has('work_event')){
			return view('elenco::index');
		}
		
		if(isset($request['id_event'])){
			$event = Event::where([['id', $request['id_event']], ['id_oratorio', Session::get('session_oratorio')]])->get();
			if(count($event)>0){
				Session::put('work_event', $event[0]->id);
				return view('elenco::index');
			}else{
				Session::flash('flash_message', 'Per vedere le iscrizioni, devi prima selezionare un evento con cui lavorare!');
				return redirect()->route('events.index');
			}
			
		}		
		

	}

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('elenco::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    	public function store(Request $request)
	{
		$input = $request->all();
		$input['colonne'] = json_encode(array());
		Elenco::create($input);
		Session::flash('flash_message', 'Elenco creato correttamente');
		return redirect()->route('elenco.index');
	}

    /**
     * Show the specified resource.
     * @return Response
     */
	public function show(Request $request)
	{
		$input = $request->all();
		$id = $input['id_elenco'];
		$elenco = Elenco::findOrFail($id);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('elenco::show')->withElenco($elenco);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
	public function edit(Request $request)
	{
		$input = $request->all();
		$id = $input['id_elenco'];
		$elenco = Elenco::findOrFail($id);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('elenco::edit')->withElenco($elenco);
		}else{
			abort(403, 'Unauthorized action.');
		}

	}

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    	public function update(Request $request)
	{
		$input = $request->all();
		$colonne = json_encode($input['colonna'], JSON_FORCE_OBJECT);
		$input['colonne'] = $colonne;
		$elenco = Elenco::findOrFail($input['id_elenco']);
		$elenco->fill($input)->save();
		Session::flash('flash_message', 'Elenco salvato!');
		return redirect()->route('elenco.edit', ['id_elenco' => $input['id_elenco']]);
	}
	
	public function save_values(Request $request){
		$input = $request->all();
		$id_values = $input['id_values'];
		$id_user = $input['id_user'];
		$users = array_keys($input['colonna']);
		$i=0;
		foreach($input['colonna'] as $c){
			$colonne = json_encode($c, JSON_FORCE_OBJECT);
			if($id_values[$i]>0){			
				$values = ElencoValue::findOrFail($input['id_values'][$i]);
				$values->valore = $colonne;
				$values->save();
			}else{
				$values = new ElencoValue;
				$values->id_user = $id_user[$i];
				$values->id_elenco = $input['id_elenco'];
				$values->valore = $colonne;
				$values->save();
			}
			$i++;
		}	
		
		Session::flash('flash_message', 'Elenco salvato!');
		return redirect()->route('elenco.show', ['id_elenco' => $input['id_elenco']]);
	}
	
	public function print(Request $request){
		$input = $request->all();
		$id = $input['id_elenco'];
		$elenco = Elenco::findOrFail($id);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('elenco::print')->withElenco($elenco);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
	
	public function report(Request $request){
		$input = $request->all();
		$elenco = Elenco::findOrFail($input['id_elenco']);
		return view('elenco::report', ['input' => $input])->withElenco($elenco);
	}

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
   	public function destroy(Request $request){
		$input = $request->all();
		$id = $input['id_elenco'];
		$elenco = Elenco::findOrFail($id);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			$elenco->delete();
	    		Session::flash("flash_message", "Elenco '". $elenco->nome."' cancellato!");
			return redirect()->route('elenco.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
	
	public function destroy_value(Request $request){
		$input = $request->all();
		$id = $input['id_value'];
		$value = ElencoValue::findOrFail($id);
		$elenco = Elenco::findOrFail($value->id_elenco);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){		
			echo $value->delete();
		}else{
			echo false;
		}
	}
	
	public function show_riempi(Request $request){
		$input = $request->all();
		$id = $input['id_elenco'];
		$elenco = Elenco::findOrFail($id);
		$event = Event::findOrFail($elenco->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('elenco::riempi')->withElenco($elenco);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
	
	public function riempi(Request $request){
		$input = $request->all();
		$elenco_val = ElencoValue::select('id_user')->where('id_elenco', $input['id_elenco'])->get()->toArray();
		if($input['nessuna']==1){
			//riempi l'elenco con tutti gli iscritti all'evento corrente
			$subs = Subscription::select('id_user')
			->where('id_event', Session::get('work_event'))
			->whereNotIn('id_user', $elenco_val)
			->get();
		}else{
			//cerco le iscrizioni che rispondono al criterio
			$subs = EventSpecValue::select('subscriptions.id_user')
				->leftJoin('subscriptions', 'subscriptions.id', 'event_spec_values.id_subscription')
				->where([['subscriptions.id_event', Session::get('work_event')], ['event_spec_values.id_eventspec', $input['spec_iscrizione']], ['event_spec_values.valore', $input['valore']]])
				->whereNotIn('subscriptions.id_user', $elenco_val)
				->get();
		}
		
		if(count($subs)>0){
			foreach($subs as $s){
				$values = new ElencoValue;
				$values->id_user = $s->id_user;
				$values->id_elenco = $input['id_elenco'];
				$values->valore = json_encode(array());
				$values->save();
			}
		}
		
		Session::flash('flash_message', 'Elenco riempito con '.count($subs).' utenti!');
		return redirect()->route('elenco.show', ['id_elenco' => $input['id_elenco']]);
	}
}
