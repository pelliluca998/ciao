<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Session;
use Entrust;
use Auth;
use Input;
use App\Week;
use App\Campo;
use App\Event;
use App\CampoWeek;
use Carbon;

class WeekController extends Controller
{
use ValidatesRequests;
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
		if(null==Session::get('work_event')){
			Session::flash("flash_message", "Prima di modificare le settimane devi selezionare un'evento!");
			return redirect()->route('events.index');
		}else{
			return view('event::week.show');
		}
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(){
		return view('event::week.create');
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request){   	
		$this->validate($request, [
			'from_date' => 'required',
			'to_date' => 'required'
		]);
		$input = $request->all();
		$input['id_event'] = Session::get('work_event');
		$date = Carbon\Carbon::createFromFormat('d/m/Y', $input['from_date']);
		$input['from_date'] = $date->format('Y-m-d');
		$date = Carbon\Carbon::createFromFormat('d/m/Y', $input['to_date']);
		$input['to_date'] = $date->format('Y-m-d');
		$week = Week::create($input);

		Session::flash('flash_message', 'Settimana aggiunta!');
		return redirect()->route('week.index');
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit(Request $request){
        $input = $request->all();
		$id = $input['id_week'];
        $week = Week::findOrFail($id);
		$event = Event::findOrFail($week->id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('event::week.edit')->withWeek($week);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update(Request $request){
		$input = $request->all();
		$week = Week::findOrFail($input['id_week']);		
       	$date = Carbon\Carbon::createFromFormat('d/m/Y', $input['from_date']);
		$input['from_date'] = $date->format('Y-m-d');
		$date = Carbon\Carbon::createFromFormat('d/m/Y', $input['to_date']);
		$input['to_date'] = $date->format('Y-m-d');
		$week->fill($input)->save();
		Session::flash('flash_message', 'Settimana salvata!');
		return redirect()->route('week.index');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy(Request $request){
        $input = $request->all();
        $id = $input['id_week'];
		$week = Week::findOrFail($id);
		$event = Event::findOrFail($week->id_event);
		if($event->id_oratorio== Session::get('session_oratorio')){
			$week->delete();		
		    	Session::flash("flash_message", "Settimana cancellata!");
			return redirect()->route('week.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}

	

    
}
