<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use Session;
use Form;
use Hash;
use Auth;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
  public $messages = [
    'display_name.required'            => 'Devi inserire un nome per il nuovo ruolo',
    'description.required'              => 'Devi inserire una descrizione per il ruolo',
    'name.unique' => 'Nome non valido, esiste già!'
  ];

  public function __construct(){
    //$this->middleware('permission:manage-users');
  }

  public function index(){
    return view('role.index');
  }

  public function create(){
    return view('role.create');
  }

  public function delete($id_role){
    $role = Role::find($id_role);
    if($role != null){
      $role->delete();
    }

    Session::flash('flash_message', 'Ruolo eliminato correttamente!');
    return redirect()->route('role.index');
  }

  public function store(Request $request){
    $input = $request->all();
    $request->merge(['name' => camel_case($input['display_name'])]);
    $this->validate($request, [
      'name' => 'unique:roles',
      'display_name' => 'required',
      'description' => 'required',
    ], $this->messages);

    $role = new Role();
    $role->name = camel_case($input['display_name']);
    $role->display_name = $input['display_name'];
    $role->description = $input['description'];
    $role->id_oratorio = Session::get('session_oratorio');
    $role->save();

    Session::flash('flash_message', 'Ruolo creato!');
    return redirect()->route('role.index');
  }

  public function updatePermission(Request $request){
    $input = $request->all();
    $roles_id = array(); //tengo traccia dei ruoli elaborati, per elaborazione successiva
    foreach($input['permesso'] as $key => $value){
      array_push($roles_id, $key);
      $role = Role::find($key);
      $role->perms()->sync($value);
    }

    //tolgo tutti i permessi ai ruoli che non compaiono nella lista precedente.
    //Bug dovuto al form: se non metto alcuna spunta ai permessi, non mi arriva nemmeno il ruolo.
    foreach(Role::whereNotIn('id', $roles_id)->get() as $role_without_perms){
      $role_without_perms->perms()->sync([]);
    }

    Session::flash('flash_message', 'Permessi aggiornati!');
    return redirect()->route('role.index');
  }
}
