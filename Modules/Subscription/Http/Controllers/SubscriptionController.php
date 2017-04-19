<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use App\Subscription;
use App\SpecSubscription;
use App\EventSpecValue;
use App\Event;
use App\UserOratorio;
use Session;
use Entrust;
use Input;
use Route;
use View;
use PDF;
use DB;
use URL;

class SubscriptionController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	public function index(Request $request){
		$input = $request->all();
		$id_event = $input['id_event'];
		Session::put('work_event', $id_event);
		return redirect()->route('subscription.index');
	}
	
	public function usersubscription(){
		return view('subscription::usersubscriptions');
		
	}
    
	public function indexCurrentEvent(){
        if(Session::has('work_event')){
            return view('subscription::show');
        }else{
            Session::flash('flash_message', 'Per vedere le iscrizioni, devi prima selezionare un evento con cui lavorare!');
            return redirect()->route('events.index');
        }

	}


	public function contact(){
		return view('subscription::contact');
	}
    
	public function selectevent(Request $request){
		$input = $request->all();
		$user_oratorio = UserOratorio::where('id_user', $input['id_user'])->get();
		if(count($user_oratorio)>0 && $user_oratorio[0]->id_oratorio == Session::get('session_oratorio')){
			return view('subscription::selectevent')->with('id_user', $input['id_user']);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}   
    
    

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request){   	
		$input = $request->all();
		$input['id_event'] = Session::get('work_event');
		$input['confirmed'] = (Input::has('confirmed')) ? true : false;
		Subscription::create($input);
		Session::flash('flash_message', 'Iscrizione avvenuta con successo!');
		return redirect()->route('subscription.index');
	}
    
	/*
	DA ELIMINARE, NON PIÙ UTILIZZATA
	public function storeselect(Request $request){
		$input = $request->all();
		$id_event = $input['id_event'];
		$users = json_decode($input['id_users']);
		if(count($users)>0){
			foreach($users as $user){
				//cerco se lo stesso utente è già iscritto. Altrimenti inserisco nuovo record
				$g = (new Subscription)->where([['id_user', '=', $user], ['id_event', '=', $id_event]])->first();
				if(count($g)==0){
					$sub = new Subscription;
					$sub->id_event=$id_event;
					$sub->id_user=$user;
					$sub->confirmed=true;
					$sub->type="ADMIN";
					$sub->save();
				}
			}
			if(!Session::has('work_event')){
				Session::put('work_event', $id_event);
			}   
			return redirect()->route('subscription.index');
		}else{
			Session::flash('flash_message', "Devi selezionare degli utenti prima di iscriverli all'evento!");
			return redirect()->route('subscription.index');
		}
	}*/


	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit(Request $request){
		$input = $request->all();
		$id = $input['id_sub'];
		$subscription = Subscription::findOrFail($id);
		$event = Event::findOrfail($sub->id_event);
		if($event->id_oratorio == Session::get('session_oratorio')){
			return view('subscription::edit')->withSubscription($subscription);
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
		$sub = Subscription::findOrFail($input['id_sub']);
		$event = Event::findOrfail($sub->id_event);
		if($event->id_oratorio == Session::get('session_oratorio')){
			$sub->fill($input)->save();
			$query = Session::get('query_param');
			Session::forget('query_param');
			Session::flash('flash_message', 'Iscrizione salvata! ');
			return redirect()->route('subscription.index', $query);
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy(Request $request){
        	$input = $request->all();
		$id = $input['id_sub'];
		$sub = Subscription::findOrFail($id);
		$event = Event::findOrfail($sub->id_event);
		//controllo che l'utente o l'amministratore abbia i permessi
		if(Auth::user()->hasRole('admin')){
			if($event->id_oratorio == Session::get('session_oratorio')){
				$sub->delete();
			}else{
				abort(403, 'Unauthorized action.');
			}
		}elseif(Auth::user()->hasRole('user')){
			if($sub->id_user == Auth::user()->id){
				$sub->delete();
			}else{
				abort(403, 'Unauthorized action.');
			}
		}
		
		Session::flash("flash_message", "Iscrizione $id cancellata!");
		$query = Session::get('query_param');
		Session::forget('query_param');
		if(Auth::user()->hasRole('admin')){
			return redirect()->route('subscription.index', $query);
		}else{
			return redirect()->route('usersubscriptions.show');
		}
	}
    
	public function subscribe_create(Request $request){
		$input = $request->all();
		//cerco se l'utente è già registrato
		Session::forget('id_subscription');
		//Session::forget('id_sub2');
		Session::forget('id_event');
		if(!isset($input['id_event'])){
			if(Session::has('work_event')){
				$input['id_event'] = Session::get('work_event');
			}else{
				Session::flash('flash_message', 'Per iscrivere l\'utente, devi prima selezionare un evento con cui lavorare!');
				return redirect()->route('events.index');
			}
		}
        	$event = Event::findOrFail($input['id_event']);
        	if($event->more_subscriptions==0){
            	$sub = (new Subscription)->where([['id_event', $id_event], ['id_user', $input['id_user']]])->get();
            	if(count($sub)>=1){
                	Session::flash('flash_message', 'Sembra che tu sia già registrato per questo evento!');
			    	return redirect('home');
            	}

        	}
        	return view('subscription::subscribe.passo1', ['event' => $event, 'id_user' => $input['id_user']]);
	}
    
	public function savesubscribe(Request $request){   	
		if(!Session::has('id_subscription')){
			$input = $request->all();
			$sub = Subscription::create($input);
			//salvo le specifiche
			$specs = $input['specs'];
			$id_spec = $input['id_spec'];
			$i=0;
			foreach($specs as $spec){
				$e = new EventSpecValue;
				$e->id_eventspec=$id_spec[$i];
				$e->valore=$spec;
				$e->id_subscription = $sub->id;
				$e->id_week=0;
				$e->save();
				$i++;
			}
			Session::put('id_subscription', $sub->id);
			Session::put('id_event', $sub->id_event);
			//Session::flash('flash_message', 'Iscrizione avvenuta con successo!');
		}
		return view('subscription::subscribe.passo2', ['id_subscription' => Session::get('id_subscription'), 'id_event' => Session::get('id_event')]);
	}
    
	public function savespecsubscribe(Request $request){
			$input = $request->all();
			//salvo le specifiche
			$valore = $input['valore'];
			$id_eventspec = $input['id_eventspec'];
			$id_week = $input['id_week'];
			$i=0;
			foreach($valore as $valore){
				$e = new EventSpecValue;
				$e->id_eventspec=$id_eventspec[$i];
				$e->valore=$valore;
				$e->id_subscription = $input['id_subscription'];
				$e->id_week = $id_week[$i];
				$e->save();
				$i++;
			}
		return view('subscription::subscribe.grazie')->with('id_subscription', Session::get('id_subscription'));
	}
	
	public function print(Request $request){
		//$html = View::make('pdf.subscription', []);
		//return PDF::loadHTML($html)->download('invoice.pdf');
		$input = $request->all();
		$id_subscription = $input['id_subscription'];		
		$sub = Subscription::findOrFail($id_subscription);
		$event = Event::findOrfail($sub->id_event);
		if($event->id_oratorio == Session::get('session_oratorio')){
			return view('subscription::printsubscription', ['id_subscription' => $id_subscription]);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
	
	public function usersub_showeventspecs(Request $request){
		$input = $request->all();
		$id_subscription = $input['id_sub'];
		$id_event = $input['id_event'];
		return view('subscription::eventspecvalue.showuser', ['id_subscription' => $id_subscription, 'id_event' => $id_event]);
	}
	
	/*public function usersub_showweekpecs(Request $request){
		$input = $request->all();
		$id_subscription = $input['id_sub'];
		$id_event = $input['id_event'];
		return view('subscription::specsubscription.showuser', ['id_sub' => $id_subscription, 'id_event' => $id_event]);
	}*/
	
	
	
	
	public function contact_send(Request $request){
		$input = $request->all();
		//$values = Input::get('spec');
		//$values_user = Input::get('spec_user');
		$type = Input::get('type');
		$filter = Input::get('filter');
		$filter_id = Input::get('filter_id');
		$filter_value = Input::get('filter_value');
		$user_filter = Input::get('user_filter');
		$user_filter_id = Input::get('user_filter_id');
		$user_filter_value = Input::get('user_filter_value');
		$att_filter = Input::get('att_filter');
		//$att_spec = Input::get('att_spec');
		$att_filter_id = Input::get('att_filter_id');
		$att_filter_value = Input::get('att_filter_value');
		
		$whereRaw = "sub.id_event = ".Session::get('work_event');
		$i=0;
		foreach($user_filter as $f){
			if($f=='1'){
				$whereRaw .= " AND users.".$user_filter_id[$i]." LIKE '%".$user_filter_value[$i]."%'";
			}
			$i++;
		}
		

		$subs = DB::table('subscriptions as sub')->select('users.id as id_user')->leftJoin('users', 'users.id', '=', 'sub.id_user')->whereRaw($whereRaw)->orderBy('users.cognome', 'asc')->orderBy('users.name', 'asc')->get();
		
		$user_array = array();
		foreach($subs as $sub){
			$r=0;
			$filter_ok=true;
			foreach($filter as $f){
				if($f==1 && $filter_ok){
					$e = EventSpecValue::where([['id_eventspec', $filter_id[$r]], ['valore', $filter_value[$r]], ['id_subscription', $sub->id_subs] ])->get();
					if(count($e)==0) $filter_ok=false;
				}
				$r++;
			}

			$r=0;
			foreach($att_filter as $fa){
				if($fa==1 && $filter_ok){
					$at = AttributoUser::where([['id_user', $sub->id_user], ['id_attributo', $att_filter_id[$r]], ['valore', $att_filter_value[$r]]])->get();
					if(count($at)==0) $filter_ok=false;
				}
				$r++;
			}
			
			if($filter_ok){
				array_push($user_array, $sub->id_user);
			}
		}
		$json = json_encode($user_array);
		if($type=='sms'){
			Session::flash('check_user', $json);
			return redirect()->route('sms.create');
		}else if($type=='email'){
			Session::flash('check_user', $json);
			return redirect()->route('email.create');
		}else if($type=='telegram'){
			Session::flash('check_user', $json);
			return redirect()->route('telegram.create');
		}
	}
}
