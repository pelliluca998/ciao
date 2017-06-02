<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use App\User;
use Session;
use Entrust;
use Carbon;
use Input;
use Excel;

class ReportController extends Controller
{

	public function eventspecreport(){
	   if(Session::has('work_event')){
		  return view('report::composer_eventspec');
	   }else{
		  Session::flash('flash_message', 'Per generare il report delle iscrizioni, devi prima selezionare un evento con cui lavorare!');
		  return redirect()->route('events.index');
	   }
	}

	public function weekreport(){
	    if(Session::has('work_event')){
		  return view('report::composer_weekspec');
	   }else{
		  Session::flash('flash_message', 'Per generare il report delle iscrizioni, devi prima selezionare un evento con cui lavorare!');
		  return redirect()->route('events.index');
	   }

	}	
	
	public function gen_eventspec(Request $request){
		$input = $request->all();
		return view('report::eventspecreport', ['input' => $input]);
	}
    
	public function gen_weekspec(Request $request){
		$input = $request->all();
		return view ('report::weekreport', ['input' => $input]);
	}
	
	public function gen_user(Request $request){
		$input = $request->all();
		return view('report::users', ['input' => $input]);
	}
	
	public function user(Request $request){
		$input = $request->all();
		return view('report::composer_user');
	}
	
}
