<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Event;
use Session;
use Input;
use Entrust;
use Image;
use File;
use Storage;

class EventController extends Controller
{
use ValidatesRequests;
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
		Session::put('work_event', $id_event);
		return redirect()->route('subscription.index');
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
			'anno' =>'required'
		]);
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
			$sub->delete();
	    		Session::flash("flash_message", "Evento '". $sub->nome."' cancellato!");
			return redirect()->route('events.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
	}		
	
	
}
