<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Role;
use App\RoleUser;
use App\AttributoUser;
use App\UserOratorio;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'username' => 'required|unique:users',
            'nato_il' => 'required|date_format:d/m/Y',
            'nato_a' => 'required',
            'cognome' => 'required',
            'sesso' => 'required',
            'residente' => 'required',
            'via' => 'required',
			'cell_number' => 'required|max:11',
            'id_oratorio' => 'required|not_in:0',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
	protected function create(array $data){
		if(!array_key_exists('id_attributo', $data)){
			$data['id_attributo'] = array();
		}
		
		if(!array_key_exists('attributo', $data)){
			$data['attributo'] = array();
		}	
    
        $id_attributo = $data['id_attributo'];
        $attributo = $data['attributo'];
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'username' => $data['username'],
            'nato_il' => $data['nato_il'],
            'nato_a' => $data['nato_a'],
            'cognome' => $data['cognome'],
            'sesso' => $data['sesso'],
            'residente' => $data['residente'],
            'via' => $data['via'],
            'cell_number' => $data['cell_number'],
        ]);
        
        //salvo l'oratorio e il ruolo solo se id_oratorio>0
		if($data['id_oratorio']>0){
			//salvo il ruolo 'user'
			$role = Role::where([['name', '=', 'user'],['id_oratorio', $data['id_oratorio']]])->first();
			$user->attachRole($role);
			//collego l'utente all'oratorio selezionato
			$useroratorio = UserOratorio::create(['id_user' => $user->id, 'id_oratorio' => $data['id_oratorio']]);
		}
		//salvo gli attributi
		$i=0;
		foreach($id_attributo as $id) {
			$attrib = AttributoUser::create(['id_user' => $user->id, 'id_attributo' => $id, 'valore' => $attributo[$i]]);
			$i++;
		}
		return $user;

    }
}
