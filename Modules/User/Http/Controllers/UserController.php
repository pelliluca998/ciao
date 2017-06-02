<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\User;
use Session;
use Entrust;
use Carbon\Carbon;
use App\RoleUser;
use Input;
use File;
use Image;
use Hash;
use App\UserOratorio;
use App\AttributoUser;
use Auth;
use Storage;

class UserController extends Controller
{
use ValidatesRequests;

	
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $check_user = Input::get('check_user');
		$json = json_encode($check_user);
		if(isset($_GET['new_user']) && $_GET['new_user']=='new_user'){
			return redirect()->route('user.create');
		}
		if(isset($_GET['email']) && $_GET['email']=='email'){
			Session::flash('check_user', $json);
			return redirect()->route('email.create');
		}
		if(isset($_GET['sms']) && $_GET['sms']=='sms'){
			Session::flash('check_user', $json);
			return redirect()->route('sms.create');
		}
		if(isset($_GET['telegram']) && $_GET['telegram']=='telegram'){
			Session::flash('check_user', $json);
			return redirect()->route('telegram.create');
		}
		if(isset($_GET['group']) && $_GET['group']=='group'){
			Session::flash('check_user', $json);
			return redirect()->route('groupusers.select');
		}
				
		if(isset($_GET['report']) && $_GET['report']=='report'){
			return view('report::composer_user');
		}

		return view('user::show');
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
	$this->validate($request, [
		'name' => 'required',
		'cognome' => 'required',
		'nato_il' => 'required|date_format:d/m/Y',
		'nato_a' => 'required',
		'email' =>'required|unique:users',
		'username' => 'unique:users',
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
	foreach($input['id_attributo'] as $id) {
		$attrib = AttributoUser::create(['id_user' => $user->id, 'id_attributo' => $id, 'valore' => $input['attributo'][$i]]);
		$i++;
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
		$user->delete();
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
}
