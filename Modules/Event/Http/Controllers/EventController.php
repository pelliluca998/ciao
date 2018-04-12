<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\Week;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Elenco\Entities\Elenco;
use Session;
use Input;
use Entrust;
use Image;
use File;
use Storage;

class EventController extends Controller
{
	use ValidatesRequests;
	public $messages = [
			'nome.required' => 'Inserisci un nome valido per l\'evento',
			'descrizione.required'  => 'Inserisci una descrizione valida',
			'anno.required'  => 'Inserisci un anno valido per l\'evento',
			'template_file.mimes'  => 'Il file template deve avere estensione .docx.',
			'image.mimes' => 'Il logo oratorio deve avere una di queste estensioni: jpeg,jpg,gif,png.'
	];

	/**
	* Display a listing of the resource.
	* @return Response
	*/
	public function index(){
		return view('event::show');
	}


	/**
	* Salva l'id_evento nella sessione
	*
	* @return Response
	*/
	public function work(Request $request){
		$input = $request->all();
		$id_event = $input['id_event'];
		$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
		$oratorio->last_id_event = $id_event;
		$oratorio->save();
		Session::put('work_event', $id_event);
		return redirect()->route('subscription.index');
	}

	/**
	** Clona un'evento in un nuovo evento, comprese tutte le specifiche
	**/

	public function clone(Request $request){
		$input = $request->all();
		$id_event = $input['id_event'];
		$event = Event::find($id_event);
		$newEvent = $event->replicate();
		$newEvent->nome = "Copia di ".$event->nome;
		$newEvent->save();
		//specs
		$event_specs = EventSpec::where('id_event', $event->id)->get();
		foreach($event_specs as $spec){
			$newSpec = $spec->replicate();
			$newSpec->id_event = $newEvent->id;
			$newSpec->save();
		}
		//weeks
		$weeks = Week::where('id_event', $event->id)->get();
		foreach($weeks as $week){
			$newWeek = $week->replicate();
			$newWeek->id_event = $newEvent->id;
			$newWeek->save();
			//aggiorno l'id delle settimane
			$event_specs = EventSpec::where('id_event', $newEvent->id)->get();
			foreach($event_specs as $spec){
				$pattern = '/"'.$week->id.'"/';
				$sostitution = '"'.$newWeek->id.'"';
				$spec->valid_for = preg_replace($pattern, $sostitution, $spec->valid_for);
				$spec->price = preg_replace($pattern, $sostitution, $spec->price);
				$spec->save();
			}
		}

		//elenco
		$elencos = Elenco::where('id_event', $event->id)->get();
			if(count($elencos)>0){
			foreach($elencos as $elenco){
				$newElenco = $elenco->replicate();
				$newElenco->id_event = $newEvent->id;
				$newElenco->save();
			}
		}

		Session::put('work_event', $newEvent->id);
		Session::flash('flash_message', 'Evento clonato correttamente!');
		return redirect()->route('events.index');

	}

	/**
	* Show the form for creating a new resource.
	* @return Response
	*/
	public function create(){
		return view('event::create');
	}

	/**
	* Store a newly created resource in storage.
	* @param  Request $request
	* @return Response
	*/
	public function store(Request $request){
		$this->validate($request, [
			'nome' => 'required',
			'descrizione' => 'required',
			'anno' =>'required',
			'template_file' => 'mimes:docx',
			'image' => 'mimes:jpeg,jpg,gif,png'
		], $this->messages);
		$input = $request->all();
		$input['id_oratorio'] = Session::get('session_oratorio');
		if(Input::hasFile('image')){
			$file = $request->image;
			$filename = $request->image->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['image'] = $filename;
		}
		$event = Event::create($input);
		Session::flash('flash_message', 'Evento aggiunto! Ora crea le informazioni che gli utenti devono dare durante l\'iscrizione');
		Session::put('work_event', $event->id);
		return redirect()->route('eventspecs.show');
	}

	public function show($id){
		$event = Event::findOrFail($id);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('subscription::show', array('id_event' => $id));
		}else{
			abort(403, 'Unauthorized action.');
		}

	}

	/**
	* Show the form for editing the specified resource.
	* @return Response
	*/
	public function edit(Request $request){
		$input = $request->all();
		$id = $input['id_event'];
		$event = Event::findOrFail($id);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('event::edit')->withEvent($event);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}

	/**
	* Update the specified resource in storage.
	* @param  Request $request
	* @return Response
	*/
	public function update(Request $request){
		$input = $request->all();
		$this->validate($request, [
			'nome' => 'required',
			'descrizione' => 'required',
			'anno' =>'required',
			'template_file' => 'mimes:docx',
			'image' => 'mimes:jpeg,jpg,gif,png'
		], $this->messages);

		$event = Event::findOrFail($input['id_event']);
		//$input['active'] = (Input::has('active') && $input['active']) ? true : false;
		//$input['more_subscriptions'] = (Input::has('more_subscriptions') && $input['more_subscriptions']) ? true : false;
		//$input['stampa_anagrafica'] = (Input::has('stampa_anagrafica') && $input['stampa_anagrafica']) ? true : false;
		if(Input::hasFile('image')){
			$file = $request->image;
			$filename = $request->image->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['image'] = $filename;
			//cancello la vecchia immagine se presente
			if($event->image!=""){
				Storage::delete('public/'.$event->image);
			}
		}

		if(Input::hasFile('template_file')){
			$file = $request->template_file;
			$filename = $request->template_file->store('template', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			//$image = Image::make($path);
			//$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			//$image->save($path);
			$input['template_file'] = $filename;
			//cancello il vecchio template
			if($event->template_file != null){
				Storage::delete('public/'.$event->template_file);
			}
		}elseif($input['elimina_template'] == 1){
			//se il checkbox "Elimina modulo caricato.." è selezionato, allora elimino il template caricato
			$input['template_file'] = null;
			if($event->template_file != null){
				Storage::delete('public/'.$event->template_file);
			}
		}


		$event->fill($input)->save();
		Session::flash('flash_message', 'Evento salvato!');
		return redirect()->route('events.index');
	}

	/**
	* Remove the specified resource from storage.
	* @return Response
	*/
	public function destroy(Request $request){
		$input = $request->all();
		$id = $input['id_event'];
		$sub = Event::findOrFail($id);
		if($sub->id_oratorio==Session::get('session_oratorio')){
			if(Session::get('work_event')==$sub->id){
				Session::forget('work_event');
			}
			$sub->delete();
			Session::flash("flash_message", "Evento '". $sub->nome."' cancellato!");
			return redirect()->route('events.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
	}


	public function strumenti(Request $request){
		if(Session::has('work_event')){
			return view('event::strumenti');
		}else{
			Session::flash('flash_message', 'Per vedere gli strumenti, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}
	}
}
