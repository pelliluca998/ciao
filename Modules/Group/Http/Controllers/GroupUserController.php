<?php

namespace Modules\Group\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Group\Entities\GroupUser;
use Modules\Group\Entities\Group;
use Modules\User\Entities\User;
use Session;
use Entrust;
use Input;
use Yajra\DataTables\DataTables;
use Modules\Group\Http\Controllers\DataTables\GroupUserDataTableEditor;
use Modules\Group\Http\Controllers\DataTables\GroupUserDataTable;

class GroupUserController extends Controller
{
	public function __construct(){
    $this->middleware('permission:view-gruppo')->only(['data', 'showcomponents']);
    $this->middleware('permission:edit-gruppo')->only(['store', 'store_user', 'select']);
  }

	public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = User::query()
    ->select('users.*', 'group_users.id as groupusers_id')
		->leftJoin('group_users', 'group_users.id_user', 'users.id')
    ->whereIn('users.id', GroupUser::where('id_group', $input['id_group'])->pluck('id_user'))
		->where('group_users.id_group', $input['id_group'])
    ->orderBy('cognome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";

			if(!Auth::user()->can('edit-gruppo')){
        $remove = "";
      }

      return $remove;
    })
    ->addColumn('DT_RowId', function ($entity){
      return $entity->groupusers_id;
    })
		->addColumn('user_label', function ($entity){
      return $entity->full_name;
    })
    ->rawColumns(['action', 'check'])
    ->toJson();
  }

	public function store(GroupUserDataTableEditor $editor){
    return $editor->process(request());
  }



	// public function create(){
	// 	$data = Input::get('id_users');
	// 	Session::flash("selected_users", $data);
	// 	return $data;
	// }

	/**
	* Assegna gli utenti al gruppo selezionato.
	*
	* @return Response
	*/
	public function store_user(Request $request){
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
	* Seleziona il gruppo in cui inserire gli utenti selezionati in anagrafica
	*/
	public function select(Request $request){
		$input = $request->all();
		$check_user = $input['users'];
		if(count(json_decode($check_user))>0){
			return view('group::groupuser.create')->with('check_user', $check_user);
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un contatto prima di inserirlo in un gruppo!");
			return redirect()->route('user.index');
		}
	}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	// public function destroy($id){
	// 	$user = GroupUser::findOrFail($id);
	// 	$group = Group::findOrfail($user->id_group);
	// 	if($group->id_oratorio == Session::get('session_oratorio')){
	// 		$user->delete();
	// 		Session::flash("flash_message", "Utente rimosso dal gruppo!");
	// 		return redirect()->route('group.index');
	// 	}else{
	// 		abort(403, 'Unauthorized action.');
	// 	}
  //
	// }

	public function showcomponents($id_group){
		return view('group::groupuser.show', ['id_group' => $id_group]);
	}
}
