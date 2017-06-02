<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Requests;
use App\Oratorio;
use App\License;
use App\OwnerMessage;
use App\Role;
use App\RoleUser;
use App\Permission;
use App\User;
use App\UserOratorio;
use Auth;
use Input;
use File;
use Image;
use Session;
use Storage;
use Mail;

class OratorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function showall()
    {
        return view('oratorio::gestione.show');
    }

	/**
	* Show the form for creating a new oratorio.
	*
	* @return Response
	*/
	public function create(){
		return view('oratorio::gestione.create');	
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	public function store(Request $request){
		$input = $request->all();
		//creo un nuovo oratorio
		$input['reg_token'] = md5($input['nome']); //è l'hash del nome, serve nella pagina di registrazione per indirizzare l'utente direttamente al suo oratorio
		$oratorio = Oratorio::create($input);
		$input['id_oratorio'] = $oratorio->id;
		//creo una nuova licenza
		$licenza = License::create($input);
		//creo due nuovi ruoli associati a questo oratorio
		$user = new Role();
		$user->name         = 'user';
		$user->display_name = 'Utente';
		$user->description  = 'User';
		$user->id_oratorio = $oratorio->id;
		$user->save();
		//creo due nuovi ruoli associati a questo oratorio
		$admin = new Role();
		$admin->name         = 'admin';
		$admin->display_name = 'Amministratore';
		$admin->description  = 'Amministratore';
		$admin->id_oratorio = $oratorio->id;
		$admin->save();
		
		//get utente da rendere amministratore
		$utente = User::findOrfail($input['id_user']);
		$utente->attachRole($admin);
		
		//Collego l'utente a questo oratorio
		$user_oratorio = UserOratorio::create($input);
		
		//sistemo i permessi
		$usermodule = Permission::where('name', 'usermodule')->first();
		$adminmodule = Permission::where('name', 'adminmodule')->first();
		$user->attachPermission($usermodule); //l'utente può accedere solo ai moduli dell'utente
		$admin->attachPermission($adminmodule);//l'admin accede ai moduli utente ed admin
		$admin->attachPermission($usermodule);
		
		//invio una mail per avvisare dell'avvenuta registrazione dell'oratorio
		Mail::send('oratorio::gestione.emailtouser', 
			['html' => 'oratorio::gestione.emailfromuser', 'user_email' => $utente->email, 'nome_oratorio' => $input['nome'], 'email_oratorio' => $input['email']], 
			function ($message) use ($input){
				$message->from('info@segresta.it', 'Segresta');
				$message->subject("Segresta - Creazione nuovo oratorio");
				$message->to($input['email'], $input['nome']);
			});
		
		
		//Fatto!
		Session::flash('flash_message', 'Oratorio creato!');
		return redirect()->route('oratorio.showall');
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
		$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
        	return view('oratorio::edit')->withOratorio($oratorio);;
    }

    /**
     * Show the form for ing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	//$user = User::find($id);
	//return view('users.edit', array('user' => $user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request){
		$input = $request->all();
		$oratorio = Oratorio::findOrFail($input['id_oratorio']);
		if(Input::hasFile('logo')){
			$file = $request->logo;
			$filename = $request->logo->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['logo'] = $filename;
			//cancello la vecchia immagine se presente
			if($oratorio->logo!=""){
				Storage::delete('public/'.$oratorio->logo);
			}
		}
		$oratorio->fill($input)->save();
		Session::flash('flash_message', 'Impostazioni salvate!');
		return redirect()->route('oratorio.index');
	}
	
	public function update_owner(Request $request){
		$input = $request->all();
		$oratorio = Oratorio::findOrFail($input['id_oratorio']);
		if(Input::hasFile('logo')){
			$file = $request->logo;
			$filename = $request->logo->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['logo'] = $filename;
			//cancello la vecchia immagine se presente
			if($oratorio->logo!=""){
				Storage::delete('public/'.$oratorio->logo);
			}
		}
		$oratorio->fill($input)->save();
		//salvo la licenza
		$licenza = License::findOrFail($input['id_licenza']);
		$licenza->fill($input)->save();
		Session::flash('flash_message', 'Impostazioni salvate!');
		return redirect()->route('oratorio.showall');
	}

    /**
     * Rimuove l'oratorio specificato
     *
     * @param  int  $id
     * @return Response
     */
	public function destroy(Request $request){
		$input = $request->all();
		$oratorio = Oratorio::findOrFail($input['id_oratorio']);
		$oratorio->delete();
		Session::flash('flash_message', 'Oratorio eliminato');
		return redirect()->route('oratorio.showall');
	}
    
    
	/**
	Richiama la pagina per la modifica dell'oratorio da parte del proprietario
	**/
	public function edit_owner(Request $request){
		$input = $request->all();
		$oratorio = Oratorio::findOrFail($input['id_oratorio']);
	   	return view('oratorio::gestione.edit')->withOratorio($oratorio);
	}
	
	public function neworatorio(Request $request){
		return view('neworatorio');
	}
	
	public function new_message(Request $request){
		return view('oratorio::gestione.add_message');
	}
	
	public function save_message(Request $request){
		$input = $request->all();
		$message = OwnerMessage::create($input);
		//invio la mail a tutti gli oratori
		$oratori = Oratorio::all();
		foreach($oratori as $o){
			Mail::send('oratorio::gestione.message', 
			['html' => 'oratorio::gestione.message', 'oratorio' => $o->nome, 'titolo' => $input['title'], 'messaggio' => $input['message']], 
			function ($message) use ($o){
				$message->from('info@segresta.it', 'Segresta');
				$message->subject("Segresta - Nuovo messaggio!");
				$message->to($o->email, $o->nome);
			});
		
		
		//Fatto!
		Session::flash('flash_message', 'Messaggio creato e email inviata!');
		return redirect()->route('oratorio.showall');
		}
	}
	
	public function neworatorio_emailfromuser(Request $request){
		$input = $request->all();
		Mail::send('oratorio::gestione.emailfromuser', 
				['html' => 'oratorio::gestione.emailfromuser', 'nome_oratorio' => $input['nome'], 'email_oratorio' => $input['email']], 
				function ($message){
					$message->from(Auth::user()->email, Auth::user()->name);
					$message->subject("Richiesta nuovo oratorio");
					$message->to('roberto.santini89@gmail.com', 'Segresta');
				});
		Session::flash('flash_message', 'Richiesta inviata! Ora attendi una risposta...');
		return redirect()->route('home');
	}
	
	public function affiliazione(Request $request){
		return view('oratorio::affiliazione');
	}
	
	public function work(Request $request){
		$input = $request->all();
		$id_oratorio = $input['id_oratorio'];
		Session::put('session_oratorio', $id_oratorio);
		return redirect()->route('home');
	}
	
	public function salva_affiliazione(Request $request){
		$input = $request->all();
		$user_role = Role::where([['name', 'user'],['id_oratorio', $input['id_oratorio']]])->first();		
		Auth::user()->attachRole($user_role);
		
		//Collego l'utente a questo oratorio		
		$user_oratorio = new UserOratorio();
		$user_oratorio->id_user = Auth::user()->id;
		$user_oratorio->id_oratorio = $input['id_oratorio'];
		$user_oratorio->save();
		
		Session::flash('flash_message', 'Fatto!');
		return redirect()->route('home');
	}
	
	public function elimina_affiliazione(Request $request){
		$input = $request->all();
		$id_useroratorio = $input['id_useroratorio'];
		$user_oratorio = UserOratorio::findOrFail($id_useroratorio);
		if($user_oratorio->id_user == Auth::user()->id){
			$role = Role::where([['name', 'user'],['id_oratorio', $user_oratorio->id_oratorio]])->first();
			$role_user = RoleUser::where([['user_id', $user_oratorio->id_user], ['role_id', $role->id]])->first();
			$role_user->delete();
			$user_oratorio->delete();
			Session::flash('flash_message', 'Affiliazione eliminata!');
			if(Session::get('session_oratorio') == $user_oratorio->id_oratorio){
				Auth::logout();
				return redirect('/');
			}else{
				return redirect()->route('oratorio.affiliazione');
			}
			
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
}
