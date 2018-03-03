<?php

namespace Modules\Group\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\GroupUser;
use Modules\User\Entities\Group;
use Session;
use Entrust;
use Input;

class GroupUserController extends Controller
{
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index(){
	}   
    

    
	public function create(){
		$data = Input::get('id_users');
		Session::flash("selected_users", $data);
		return $data;
	}

	/**
	* Assegna gli utenti al gruppo selezionato.
	*
	* @return Response
	*/
	public function store(Request $request){   	
		$input = $request->all();
		$id_group = $input['id_gruppo'];
		$users = json_decode($input['check_user']);
		foreach($users as $user){
			//cerco se lo stesso utente è già assegnato allo stesso gruppo. Altrimenti inserisco nuovo record
			$g = (new GroupUser)->where([['id_user', '=', $user], ['id_group', '=', $id_group]])->first();
			if(count($g)==0){
				$groupUser = new GroupUser;
			$groupUser->id_group=$id_group;
			$groupUser->id_user = $user;
			$groupUser->save();
			}
		}        
		return redirect()->route('group.index');
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function select(Request $request){
		$input = $request->all();
		$check_user = $input['check'];
		if(count(json_decode($check_user))>0){
			return view('group::groupuser.create')->with('check_user', $check_user);
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un contatto prima di inserirlo in un gruppo!");
			return redirect()->route('user.index');
		}
		
	}
	
	public function show($id){
	}

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	public function destroy($id){
		$user = GroupUser::findOrFail($id);
		$group = Group::findOrfail($user->id_group);
		if($group->id_oratorio == Session::get('session_oratorio')){
			$user->delete();
			Session::flash("flash_message", "Utente rimosso dal gruppo!");
			return redirect()->route('group.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}
	
	public function showcomponents($id_group){
		return view('group::groupuser.show', ['id_group' => $id_group]);
	}
}
