<?php

namespace Modules\Group\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Group;
use App\GroupUser;
use Session;
use Entrust;
use Input;

class GroupController extends Controller
{
    
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
		if(isset($_GET['email']) && $_GET['email']=='email'){
			$checks = Input::get('check_groups');
			$id_users=array();
			foreach($checks as $group){
				$us = GroupUser::select('id_user')->where('id_group', $group)->get();
				foreach($us as $u){
					array_push($id_users, $u->id_user);
				}
			}
			return redirect()->route('emails.new', json_encode($id_users));
		}else if(isset($_GET['new']) && $_GET['new']=='new'){
            return redirect()->route('group.create');
        }else{
			return view('group::show');
		}
	}   
    

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(){
		return view('group::create');
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request){   	
		$input = $request->all();
		Group::create($input);
		Session::flash('flash_message', 'Gruppo creato!');
		return redirect()->route('group.index');
	}


	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit(Request $request){
        $input = $request->all();
        $group = Group::where('id', $input['id_group'])->first();
		if($group->id_oratorio==Session::get('session_oratorio')){
            return view('group::edit')->withGroup($group);
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
		$sub = Group::findOrFail($input['id_group']);	
		$sub->fill($input)->save();
		Session::flash('flash_message', 'Gruppo salvato!');
		return redirect()->route('group.index');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy(Request $request){
        $input = $request->all();
        $id = $input['id_group'];
        $group = Group::where('id', $id)->first();
		if($group->id_oratorio==Session::get('session_oratorio')){
            $group->delete();
            Session::flash("flash_message", "Gruppo '". $group->nome."' cancellato!");
		    return redirect()->route('group.index');
        }else{
			abort(403, 'Unauthorized action.');
		}
	}
	
	public function report_composer(Request $request){
        $input = $request->all();
        $id = $input['id_group'];
        $group = Group::where('id', $id)->first();
		if($group->id_oratorio==Session::get('session_oratorio')){
            return view('group::report_composer', ['id_group' => $id]);
        }else{
			abort(403, 'Unauthorized action.');
		}

	}
	
	public function report_generator(Request $request){
		$input = $request->all();
		$values = Input::get('spec');
		$id_group = Input::get('id_group');


		return view('group::report_generator', ['input' => $input]);
	}

}
