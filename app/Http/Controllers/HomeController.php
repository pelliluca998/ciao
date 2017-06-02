<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Entrust;
use Session;
use App\UserOratorio;
use App\Oratorio;
use Auth;
use Input;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	if(Session::get('session_oratorio')==null || Session::get('session_oratorio')==''){
			//get id_oratorio from user_oratorio table.
			//if user is linked to multiple oratorios, show the page to select correct oratorio
			$oratorios = UserOratorio::where('id_user', Auth::user()->id)->get();
			if(count($oratorios)==0){
				//non esiste l'associazione utente-oratorio, può essere un nuovo oratorio
				return view('home')->with('oratorio', -1);
			}else if(count($oratorios)==1){
				Session::put('session_oratorio', $oratorios[0]->id_oratorio);
			}else{
				//show window to select oratorio
				$oratorio = array();
				foreach($oratorios as $o){
					array_push($oratorio, $o->id_oratorio);
				}
				return view('home')->with('oratorio', $oratorio);
			}

		}
		if(Auth::user()->hasRole('user')){
			return view('home');
		}else{			
			return redirect()->route('admin');
		}
		

    }
	
	public function admin(){
		if(Session::get('session_oratorio')==null || Session::get('session_oratorio')==''){
			return redirect()->route('home');
		}
		//registro un nuovo accesso, poi rimando alla pagina admin
		$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
		$oratorio->last_login = date('Y-m-d H:i:s', time());
		$oratorio->save();
		return view('admin');
	}
	
	public function licenza(){
		return view('licenza');
	}
	
	public function select_oratorio(Request $request){
		$id_oratorio = Input::get('id_oratorio');
		Session::put('session_oratorio', $id_oratorio);
		return redirect('home');
	}
	
	
}
