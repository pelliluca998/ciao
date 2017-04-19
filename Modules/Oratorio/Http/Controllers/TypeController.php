<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Type;
use App\TypeSelect;
use Session;
use Entrust;
use Input;
use Auth;

class TypeController extends Controller
{
    
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
		return view('oratorio::type.show');
	}	
    

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(){
		return view('oratorio::type.create');
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request){   	
		$input = $request->all();
		Type::create($input);
		//To Do Aggiungere un event_spec_values di questo tipo per ogni iscrizione
		Session::flash('flash_message', 'Elenco creato!');
		return redirect()->route('type.index');
	}

    

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit(Request $request){
        $input = $request->all();
		$type = Type::findOrFail($input['id_type']);
		if($type->id_oratorio==Session::get('session_oratorio')){
			return view('oratorio::type.edit')->withType($type);
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
		$sub = Type::findOrFail($input['id_type']);		
		$sub->fill($input)->save();
		Session::flash('flash_message', 'Elenco salvato!');
		return redirect()->route('type.index');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy(Request $request){
        $input = $request->all();
		$sub = Type::findOrFail($input['id_type']);
		if($sub->id_oratorio==Session::get('session_oratorio')){
			$sub->delete();
			Session::flash("flash_message", "Elenco ".$input['id_type']." cancellato!");
			return redirect()->route('type.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}
}
