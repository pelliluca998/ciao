<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\User\Entities\User;
use Session;
use Entrust;
use Carbon\Carbon;
use App\RoleUser;
use App\Role;
use Input;
use File;
use Image;
use Hash;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\Attributo\Entities\AttributoUser;
use Auth;
use Storage;
use Excel;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
  use ValidatesRequests;


  /**
  * Display a listing of the resource.
  * @return Response
  */
  public function index()
  {
    return view('user::show');
  }

  public function action(Request $request){
    $input = $request->all();
    if(!$request->has('check_user')){
      Session::flash("flash_message", "Devi selezionare almeno un utente!");
      return redirect()->route('user.index');
    }
    $check_user = $input['check_user'];
    $json = json_encode($check_user);
    switch($input['action']){
      case 'email':
      return redirect()->route('email.create', ['users' => $json]);
      break;
      case 'sms':
      return redirect()->route('sms.create', ['users' => $json]);
      break;
      case 'telegram':
      return redirect()->route('telegram.create', ['users' => $json]);
      break;
      case 'group':
      return redirect()->route('groupusers.select', ['users' => $json]);
      break;
      case 'whatsapp':
      return redirect()->route('whatsapp.create', ['users' => $json]);
      break;
    }
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = User::query()
    ->select('users.*')
    ->leftJoin('user_oratorio', 'user_oratorio.id_user', 'users.id')
    ->where('user_oratorio.id_oratorio', Session::get('session_oratorio'))
    ->orderBy('cognome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($user){
      $action = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#userOp' data-username='".$user->name." ".$user->cognome."' data-userid='".$user->id."'><i class='fas fa-cogs fa-2x'></i> </button>";
      return $action;
    })
    ->addColumn('check', function ($user){
      $check = "<input name='check_user[]' id='check_users_".$user->id."' type='checkbox' value='".$user->id."' class='form-control'/>";
      return $check;
    })
    ->addColumn('photo', function ($user){
      if($user->photo==null){
        if($user->sesso=="M"){
          return "<img src='".url("boy.png")."'>";
        }else if($user->sesso=="F"){
          return "<img src='".url("girl.png")."'>";
        }
      }else{
        return "<img src='".url(Storage::url('public/'.$user->photo))."' width=48px/>";
      }
    })
    ->rawColumns(['photo', 'action', 'check'])
    ->toJson();
  }

  /**
  * Show the form for creating a new resource.
  * @return Response
  */
  public function create()
  {
    return view('user::create');
  }

  public function print_userprofile(Request $request){
    $input = $request->all();
    $id_user = $input['id_user'];
    return view('user::userprofile')->with('id_user', $id_user);
  }

  /**
  * Store a newly created resource in storage.
  * @param  Request $request
  * @return Response
  */
  public function store(Request $request)
  {
    //Genera user, password e email se non forniti
    if(isset($request['genera_email'])){
      $user = new User;
      $email = "";
      do{
        $email = str_random(20);
        $user = User::where([['email', $email.'@segresta.it'], ['username', $email]])->get();
      }while(count($user)>0);

      $request['email'] = $email.'@segresta.it';
      $request['username'] = $email;
    }

    if(isset($request['genera_password'])){
      $request['password'] = str_random(40);
    }

    $this->validate($request, [
      'name' => 'required',
      'cognome' => 'required',
      'nato_il' => 'required|date_format:d/m/Y',
      'nato_a' => 'required',
      'email' =>'required|unique:users',
      'username' => 'required|unique:users',
      'password' => 'required'
    ]);
    $input = $request->all();
    $date = Carbon::createFromFormat('d/m/Y', $input['nato_il']);
    if(Input::hasFile('photo')){
      $file = $request->photo;
      $filename = $request->photo->store('profile', 'public');
      $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
      $image = Image::make($path);
      $image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
      $image->save($path);
      $input['photo'] = $filename;
    }
    $input['password'] = Hash::make($input['password']);
    //salvo l'utente
    $user = User::create($input);
    //salvo il link utente-oratorio
    $orat = new UserOratorio;
    $orat->id_user=$user->id;
    $orat->id_oratorio = Session::get('session_oratorio');
    $orat->save();

    //salvo attributi
    $i=0;
    if(isset($input['id_attributo']) && count($input['id_attributo'])>0){
      foreach($input['id_attributo'] as $id) {
        $attrib = AttributoUser::create(['id_user' => $user->id, 'id_attributo' => $id, 'valore' => $input['attributo'][$i]]);
        $i++;
      }
    }

    //aggiungo il ruolo
    $roles = Role::where([['name', 'user'], ['id_oratorio', Session::get('session_oratorio')]])->get();
    if(count($roles)>0){
      //creo il ruolo
      $role = new RoleUser;
      $role->user_id = $user->id;
      $role->role_id = $roles[0]->id;
      $role->save();
    }
    Session::flash('flash_message', 'Utente aggiunto!');
    return redirect()->route('user.index');
  }

  /**
  * Show the specified resource.
  * @return Response
  */
  public function show()
  {
    return view('user::show');
  }

  /**
  * Show the form for editing the specified resource.
  * @return Response
  */
  public function edit(Request $request)
  {
    $input = $request->all();
    $id = $input['id_user'];
    $user = User::findOrFail($id);
    $orat = UserOratorio::where([['id_user', $user->id], ['id_oratorio', Session::get('session_oratorio')]])->get();
    if(count($orat)>0){
      return view('user::edit')->withUser($user);
    }else{
      abort(403, 'Unauthorized action.');
    }
  }

  public function profile(){
    $user = User::findOrFail(Auth::user()->id);
    return view('user::profile')->withUser($user);
  }

  /**
  * Update the specified resource in storage.
  * @param  Request $request
  * @return Response
  */
  public function update(Request $request){
    $input = $request->all();
    $user = User::findOrFail($input['id_user']);

    $this->validate($request, [
      'name' => 'required',
      'cognome' => 'required',
      'nato_il' => 'required|date_format:d/m/Y',
      'nato_a' => 'required',
      'email' => 'required|email|unique:users,email,'.$user->id,
      'username' => 'required|unique:users,username,'.$user->id,
    ]);

    $orat = UserOratorio::where('id_user', $user->id)->first();
    if($orat->id_oratorio==Session::get('session_oratorio') || Auth::user()->id==$input['id_user']){

      if(Input::hasFile('photo')){
        $file = $request->photo;
        $filename = $request->photo->store('profile', 'public');
        $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
        $image = Image::make($path);
        $image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
        $image->save($path);
        $input['photo'] = $filename;
        //cancello la vecchia immagine se presente
        if($user->photo!=""){
          Storage::delete('public/'.$user->photo);
        }
      }
      if(strlen($input['password'])>0){
        $input['password'] = Hash::make($input['password']);
      }else{
        unset($input['password']);
      }
      $user->fill($input)->save();
      Session::flash('flash_message', 'Utente salvato!');
      $query = Session::get('query_param');
      Session::forget('query_param');
      //salvo ruolo
      //$user->roles()->sync(array($input['id_role']));
      $role = RoleUser::where([['user_id', $user->id],['role_id', $user->roles[0]->id]])->first();
      $role->role_id = $input['id_role'];
      $role->save();

      //Salvo gli attributi
      $keys = ['id_attributo', 'id_attributouser', 'valore'];
      foreach($keys as $key){
        if(!array_key_exists($key, $input)){
          $input[$key] = array();
        }
      }
      $id_attributo = $input['id_attributo'];
      $id_attributouser = $input['id_attributouser'];
      $valore = $input['valore'];
      $i=0;
      foreach($id_attributo as $id){
        if($id_attributouser[$i]>0){
          $u = AttributoUser::findOrfail($id_attributouser[$i]);
          $u->valore = $valore[$i];
          $u->save();
        }else{
          $u = new AttributoUser();
          $u->id_user = $user->id;
          $u->id_attributo = $id_attributo[$i];
          $u->valore = $valore[$i];
          $u->save();
        }
        $i++;
      }

      //end salvo ruolo
      if(Auth::user()->hasRole('user')){
        return redirect()->route('home');
      }else{
        return redirect()->route('user.index', $query);
      }

    }else{
      abort(403, 'Unauthorized action.');
    }
  }

  /**
  * Remove the specified resource from storage.
  * @return Response
  */
  public function destroy(Request $request)
  {
    $input = $request->all();
    $user = User::findOrFail($input['id_user']);
    $orat = UserOratorio::where('id_user', $user->id)->first();
    if($orat->id_oratorio==Session::get('session_oratorio')){
      $orat->delete();
      //$user->delete();
      Session::flash("flash_message", "Utente cancellato!");
      $query = Session::get('query_param');
      Session::forget('query_param');
      return redirect()->route('user.index', $query);
    }else{
      abort(403, 'Unauthorized action.');
    }
  }

  public function updateprofile(Request $request){
    $input = $request->all();
    $user = User::findOrFail($input['id']);
    if(Auth::user()->id==$user->id){
      if(Input::hasFile('photo')){
        $file = $request->photo;
        $filename = $request->photo->store('profile', 'public');
        $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
        $image = Image::make($path);
        $image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
        $image->save($path);
        $input['photo'] = $filename;
        //cancello la vecchia immagine se presente
        if($user->photo!=""){
          Storage::delete('public/'.$user->photo);
        }
      }
      if(strlen($input['password'])>0){
        $input['password'] = Hash::make($input['password']);
      }else{
        unset($input['password']);
      }
      $user->fill($input)->save();
      //Salvo gli attributi
      $keys = ['id_attributo', 'id_attributouser', 'valore'];
      foreach($keys as $key){
        if(!array_key_exists($key, $input)){
          $input[$key] = array();
        }
      }
      $id_attributo = $input['id_attributo'];
      $id_attributouser = $input['id_attributouser'];
      $valore = $input['valore'];
      $i=0;
      foreach($id_attributo as $id){
        if($id_attributouser[$i]>0){
          $u = AttributoUser::findOrfail($id_attributouser[$i]);
          $u->valore = $valore[$i];
          $u->save();
        }else{
          $u = new AttributoUser();
          $u->id_user = $user->id;
          $u->id_attributo = $id_attributo[$i];
          $u->valore = $valore[$i];
          $u->save();
        }
        $i++;
      }
      //Redirect
      Session::flash('flash_message', 'Profilo salvato!');
      $query = Session::get('query_param');
      Session::forget('query_param');
      return redirect()->route('home');
    }else{
      abort(403, 'Unauthorized action.');
    }


  }

  public function sistema_permessi(){
    $user_oratorio = UserOratorio::all();
    foreach($user_oratorio as $uo){
      $role = RoleUser::where('user_id', $uo->id_user)->get();
      if(count($role)==0){
        $roles = Role::where([['name', 'user'], ['id_oratorio', $uo->id_oratorio]])->get();
        if(count($roles)>0){
          //creo il ruolo
          $role = new RoleUser;
          $role->user_id = $uo->id_user;
          $role->role_id = $roles[0]->id;
          $role->save();
        }
      }
    }
  }


}
