<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use App\Subscription;
use App\EventSpec;
use App\Oratorio;
use App\SpecSubscription;
use App\EventSpecValue;
use App\Event;
use App\UserOratorio;
use App\User;
use App\Bilancio;
use App\Cassa;
use App\ModoPagamento;
use App\TipoPagamento;
use App\License;
use App\Week;
use Module;
use Session;
use Entrust;
use Input;
use Route;
use View;
use PDF;
use DB;
use URL;
use Mail;
use Storage;

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
		$user_oratorio = UserOratorio::where([['id_user', $input['id_user']], ['id_oratorio', Session::get('session_oratorio')]])->get();
		if(count($user_oratorio)>0){
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
		$event = Event::findOrfail($subscription->id_event);
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
			if($sub->confirmed==0 && $input['confirmed']==1){
				//mando la mail all'utente
				$user = User::findOrFail($sub->id_user);
				$event = Event::findOrFail($sub->id_event);
				Mail::send('subscription::confirmed_email',
				['html' => 'subscription::confirmed_email', 'event_name' => $event->nome, 'user' => $user->full_name],
				function ($message) use ($user){
					$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
					$message->from($oratorio->email, $oratorio->nome);
					$message->subject("La tua iscrizione è stata approvata");
					$message->to($user->email, $user->full_name);
				});
			}
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

		$this->delete_subscription($id);

		Session::flash("flash_message", "Iscrizione $id cancellata!");
		$query = Session::get('query_param');
		Session::forget('query_param');
		if(Auth::user()->hasRole('user')){
			return redirect()->route('usersubscriptions.show');
		}else{
			return redirect()->route('subscription.index', $query);
		}
	}

	function delete_subscription($id_sub){
		$sub = Subscription::findOrFail($id_sub);
		$event = Event::findOrfail($sub->id_event);
		//controllo che l'utente o l'amministratore abbia i permessi
		if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner')){
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
			$sub = (new Subscription)->where([['id_event', $event->id], ['id_user', $input['id_user']]])->get();
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
			$costo = $input['costo'];
			$pagato = $input['pagato'];
			$i=0;
			foreach($specs as $spec){
				$e = new EventSpecValue;
				$e->id_eventspec=$id_spec[$i];
				$e->valore=$spec;
				$e->id_subscription = $sub->id;
				$e->id_week=0;
				$e->costo = floatval($costo[$i]);
				$e->pagato = $pagato[$i];
				$e->save();

				//contabilita
				if(Module::find('contabilita')!=null && License::isValid('contabilita') && !Auth::user()->hasRole('user')){
					if($pagato[$i]==1){
						//pagamento avvenuto ora, salvo una riga in bilancio
						$id_cassa=0;
						$id_modo=0;
						$id_tipo=0;
						$event_spec = EventSpec::where('id', $e->id_eventspec)->first();
						if($event_spec->id_cassa!=null){
							$id_cassa = $event_spec->id_cassa;
						}
						if($event_spec->id_modopagamento!=null){
							$id_modo = $event_spec->id_modopagamento;
						}
						if($event_spec->id_tipopagamento!=null){
							$id_tipo = $event_spec->id_tipopagamento;
						}
						$bilancio = new Bilancio;
						$bilancio->id_event = $input['id_event'];
						$bilancio->id_user = Auth::user()->id;
						$bilancio->id_eventspecvalues = $e->id;
						$bilancio->id_tipopagamento = $id_tipo;
						$bilancio->id_modalita = $id_modo;
						$bilancio->id_cassa = $id_cassa;
						$user = User::findOrFail($input['id_user']);
						$bilancio->descrizione = "Pagamento da ".$user->cognome." ".$user->name." (iscrizione #".$sub->id.")";
						$bilancio->importo = floatval($costo[$i]);
						$bilancio->data = date('Y-m-d');
						$bilancio->save();
					}
				}
				//endcontabilita

				$i++;
			}



			Session::put('id_subscription', $sub->id);
			Session::put('id_event', $sub->id_event);
		}
		return view('subscription::subscribe.passo2', ['id_subscription' => Session::get('id_subscription'), 'id_event' => Session::get('id_event')]);
	}

	public function savespecsubscribe(Request $request){
		$input = $request->all();
		//salvo le specifiche
		if(isset($input['valore'])){
			$valore = $input['valore'];
			$id_eventspec = $input['id_eventspec'];
			$id_week = $input['id_week'];
			$costo = $input['costo_2'];
			$pagato = $input['pagato_2'];
			$user = User::findOrFail(Subscription::findOrFail($input['id_subscription'])->id_user);
			$i=0;
			foreach($valore as $valore){
				$e = new EventSpecValue;
				$e->id_eventspec=$id_eventspec[$i];
				$e->valore=$valore;
				$e->id_subscription = $input['id_subscription'];
				$e->id_week = $id_week[$i];
				$e->pagato = $pagato[$i];
				$e->costo = $costo[$i];
				$e->save();

				//contabilita
				if(Module::find('contabilita')!=null && License::isValid('contabilita') && !Auth::user()->hasRole('user')){
					if($pagato[$i]==1){
						//pagamento avvenuto ora, salvo una riga in bilancio
						$id_cassa=0;
						$id_modo=0;
						$id_tipo=0;
						$event_spec = EventSpec::where('id', $e->id_eventspec)->first();
						if($event_spec->id_cassa!=null){
							$id_cassa = $event_spec->id_cassa;
						}
						if($event_spec->id_modopagamento!=null){
							$id_modo = $event_spec->id_modopagamento;
						}
						if($event_spec->id_tipopagamento!=null){
							$id_tipo = $event_spec->id_tipopagamento;
						}
						$bilancio = new Bilancio;
						$bilancio->id_event = $input['id_event'];
						$bilancio->id_user = Auth::user()->id;
						$bilancio->id_eventspecvalues = $e->id;
						$bilancio->id_tipopagamento = $id_tipo;
						$bilancio->id_modalita = $id_modo;
						$bilancio->id_cassa = $id_cassa;
						$bilancio->descrizione = "Pagamento da ".$user->cognome." ".$user->name." (iscrizione #".$input['id_subscription'].")";
						$bilancio->importo = floatval($costo[$i]);
						$bilancio->data = date('Y-m-d');
						$bilancio->save();
					}
				}
				//endcontabilita

				$i++;
			}
		}
		return view('subscription::subscribe.grazie')->with('id_subscription', Session::get('id_subscription'));
	}

	// public function print(Request $request){
	// 	//$html = View::make('pdf.subscription', []);
	// 	//return PDF::loadHTML($html)->download('invoice.pdf');
	// 	$input = $request->all();
	// 	$id_subscription = $input['id_sub'];
	// 	$sub = Subscription::findOrFail($id_subscription);
	// 	$event = Event::findOrfail($sub->id_event);
	// 	if($event->id_oratorio == Session::get('session_oratorio')){
	// 		return view('subscription::printsubscription', ['id_subscription' => $id_subscription]);
	// 	}else{
	// 		abort(403, 'Unauthorized action.');
	// 	}
	// }

	public function usersub_showeventspecs(Request $request){
		$input = $request->all();
		$id_subscription = $input['id_sub'];
		$id_event = $input['id_event'];
		return view('subscription::eventspecvalue.showuser', ['id_subscription' => $id_subscription, 'id_event' => $id_event]);
	}




	public function contact_send(Request $request){
		$input = $request->all();
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

		$whereRaw = "subscriptions.id_event = ".Session::get('work_event');
		$i=0;
		foreach($user_filter as $f){
			if($f=='1'){
				$whereRaw .= " AND users.".$user_filter_id[$i]." LIKE '%".$user_filter_value[$i]."%'";
			}
			$i++;
		}

		$subs = Subscription::select('subscriptions.id_user', 'subscriptions.id as id_subs')
		->leftJoin('users', 'users.id', '=', 'subscriptions.id_user')
		->whereRaw($whereRaw)
		->orderBy('users.cognome', 'asc')
		->orderBy('users.name', 'asc')
		->get();

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
			if(count($att_filter)>0){
				foreach($att_filter as $fa){
					if($fa==1 && $filter_ok){
						$at = AttributoUser::where([['id_user', $sub->id_user], ['id_attributo', $att_filter_id[$r]], ['valore', $att_filter_value[$r]]])->get();
						if(count($at)==0) $filter_ok=false;
					}
					$r++;
				}
			}

			if($filter_ok){
				array_push($user_array, $sub->id_user);
			}
		}

		$json = json_encode(array_values(array_unique($user_array)));
		if($type=='sms'){
			return redirect()->route('sms.create', ['check' => $json]);
		}else if($type=='email'){
			return redirect()->route('email.create', ['check' => $json]);
		}else if($type=='telegram'){
			return redirect()->route('telegram.create', ['check' => $json]);
		}
	}

	public function approve(Request $request){
		$input = $request->all();
		$check_user = $input['check'];
		if(count(json_decode($check_user))>0){
			$subs = Subscription::where('confirmed', 0)->whereIn('id', json_decode($check_user))->get();
			foreach($subs as $sub){
				$sub->confirmed=1;
				$sub->save();
				//mando la mail all'utente
				$user = User::findOrFail($sub->id_user);
				$event = Event::findOrFail($sub->id_event);
				Mail::send('subscription::confirmed_email',
				['html' => 'subscription::confirmed_email', 'event_name' => $event->nome, 'user' => $user->full_name],
				function ($message) use ($user){
					$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
					$message->from($oratorio->email, $oratorio->nome);
					$message->subject("La tua iscrizione è stata approvata");
					$message->to($user->email, $user->full_name);
				});
			}

			Session::flash("flash_message", "Hai approvato ".count(json_decode($check_user))." iscrizioni!");
			return redirect()->route('subscription.index');
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un'iscrizione prima di approvarle!");
			return redirect()->route('subscription.index');
		}


	}

	public function batch_delete(Request $request){
		$input = $request->all();
		$check_user = $input['check'];
		$subs = json_decode($check_user);
		if(count($subs)>0){
			foreach($subs as $sub){
				$this->delete_subscription($sub);
				//echo $sub."<br>";
			}
			Session::flash("flash_message", "Hai cancellato ".count($subs)." iscrizioni!");
			return redirect()->route('subscription.index');
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un'iscrizione prima di cancellarle!");
			return redirect()->route('subscription.index');
		}


	}

	public function print_subscription(Request $request){
		$input = $request->all();
		//Se l'id_subscription è preview, allora devo stampare Anteprima del template senza i dati dell'iscrizione
		if($input['id_subscription']=='preview'){
			$event = Event::findOrFail(Session::get('work_event'));
			if($event->template_file == null){
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/template/subscription_template.docx')));
			}else{
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/'.$event->template_file)));
			}
			$oratorio = Oratorio::findOrFail($event->id_oratorio);
			$template->setValue('nome_oratorio', $oratorio->nome);
			$template->setValue('nome_evento', $event->nome);
			$template->setValue('id_subscription', "Anteprima");
			//specifiche generali
			$specs = (new EventSpec)->select()
			->where([['event_specs.id_event', $event->id], ['event_specs.general', 1]])
			->orderBy('event_specs.ordine', 'asc')->get();
			if(count($specs)>0){
				$template->cloneRow('specifica_g', count($specs));
				$i = 1;
				foreach($specs as $spec){
					$template->setValue('specifica_g#'.$i, $spec->label);
					$template->setValue('valore_g#'.$i, '');
					$costi = json_decode($spec->price, true);
					if(isset($costi['0'])){
						$costo = $costi['0'];
					}else{
						$costo = 0;
					}
					$template->setValue('costo_g#'.$i, $costo."€");
					if($costo == 0){
						$template->setValue('pagato_g#'.$i, '');
					}else{
						$template->setValue('pagato_g#'.$i, 'NO');
					}

					$i++;
				}
			}

			//specifiche Settimanali
			$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $event->id)->orderBy('from_date', 'asc')->get();
			if(count($weeks)>0){
				//clono il blocco settimana per il numero di settimane trovate
				$template->cloneBlock('settimana', count($weeks));
				$w = 1;
				foreach($weeks as $week){
					$i = 1;
					$template->setValue('nome_settimana#'.$w, "Settimana $w - dal ".$week->from_date." al ".$week->to_date);
					$specs = (new EventSpec)->select()
					->where([['event_specs.id_event', $event->id], ['event_specs.general', 0]])
					->orderBy('event_specs.ordine', 'asc')->get();
					if(count($specs)>0){
						//prima di clonare la riga, devo sapere quante sono dal json_decode
						$c=0;
						foreach($specs as $spec){
							$valid = json_decode($spec->valid_for, true);
							if(isset($valid[$week->id]) && $valid[$week->id] == 1) $c++;
						}
						if($c>0) $template->cloneRow('specifica_w#'.$w, $c);
						//ora posso popolare la tabella clonata
						foreach($specs as $spec){
							$valid = json_decode($spec->valid_for, true);
							if(isset($valid[$week->id]) && $valid[$week->id] == 1){
								$template->setValue('specifica_w#'.$w.'#'.$i, $spec->label);
								$template->setValue('valore_w#'.$w.'#'.$i, '');
								$costi = json_decode($spec->price, true);
								if(isset($costi[$week->id])){
									$costo = $costi[$week->id];
								}else{
									$costo = 0;
								}
								$template->setValue('costo_w#'.$w.'#'.$i, $costo."€");
								if($costo == 0){
									$template->setValue('pagato_w#'.$w.'#'.$i, '');
								}else{
									$template->setValue('pagato_w#'.$w.'#'.$i, 'NO');
								}
								$i++;
							}
						}
					}
					$w++;
				}
			}
		}else{
			$sub = Subscription::findOrFail($input['id_subscription']);
			$event = Event::findOrFail($sub->id_event);
			$oratorio = Oratorio::findOrFail($event->id_oratorio);
			$user = User::findOrFail($sub->id_user);
			if($event->template_file == null){
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/template/subscription_template.docx')));
			}else{
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/'.$event->template_file)));
			}
			//verifico che l'iscrizione sia dell'utente loggato, oppure se amministratore che sia il suo Oratorio
			if(Auth::user()->hasRole('user')){
				if($sub->id_user != Auth::user()->id){
					Session::flash("flash_message", "Operazione non valida!");
					return redirect()->route('subscription.index');
				}
			}else{
				if($oratorio->id != Session::get('session_oratorio')){
					Session::flash("flash_message", "Operazione non valida!");
					return redirect()->route('subscription.index');
				}
			}
			//end verifica
			//controllo se nel modulo devono essere stampati i dati anagrafici o, al loro posto, il valore di una specifica
			if($event->stampa_anagrafica == 1){
				$template->setValue('nominativo', '');
				$template->cloneBlock('dati_anagrafici');
			}else{
				$template->replaceBlock('dati_anagrafici', 'dati2');
				$spec = EventSpecValue::where('id_eventspec', $event->spec_iscrizione)->where('id_subscription', $sub->id)->get();
		    if(count($spec)>0){
		       $template->setValue('nominativo', $spec[0]->valore);
		    }else{
					$template->setValue('nominativo', 'Valore non valido!');
				}
			}

			$template->setValue('nome_oratorio', $oratorio->nome);
			$template->setValue('nome_evento', $event->nome);
			$template->setValue('id_subscription', $sub->id);
			$template->setValue('cognome#1', $user->cognome);
			$template->setValue('nome#1', $user->name);
			$template->setValue('nato_a#1', $user->nato_a);
			$template->setValue('nato_il#1', $user->nato_il);
			$template->setValue('via#1', $user->via);
			$template->setValue('residente#1', $user->residente);

			//specifiche generali
			$importo_totale = 0;
			$da_pagare = 0;
			$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.pagato')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->where([['event_spec_values.id_subscription', $sub->id], ['event_specs.general', 1]])
			->orderBy('event_specs.ordine', 'asc')->get();
			if(count($specs)>0){
				$template->cloneRow('specifica_g', count($specs));
				$i = 1;
				foreach($specs as $spec){
					$template->setValue('specifica_g#'.$i, $spec->label);
					$template->setValue('valore_g#'.$i, EventSpec::getPrintableValue($spec->id_type, $spec->valore));
					$template->setValue('costo_g#'.$i, $spec->costo);
					$importo_totale += $spec->costo;
					if($spec->costo == 0){
						$template->setValue('pagato_g#'.$i, '');
					}else{
						if($spec->pagato == 1){
							$template->setValue('pagato_g#'.$i, 'SI');
						}else{
							$template->setValue('pagato_g#'.$i, 'NO');
						}

					}
					if($spec->pagato == false){
						$da_pagare += $spec->costo;
					}

					$i++;
				}
			}

			//specifiche Settimanali
			$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $sub->id_event)->orderBy('from_date', 'asc')->get();
			if(count($weeks)>0){
				$template->cloneBlock('settimana', count($weeks));
				$w = 1;
				foreach($weeks as $week){
					$i = 1;
					$template->setValue('nome_settimana#'.$w, "Settimana $w - dal ".$week->from_date." al ".$week->to_date);
					$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.pagato', 'event_specs.valid_for')
					->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
					->where([['event_spec_values.id_subscription', $sub->id], ['event_specs.general', 0], ['event_spec_values.id_week', $week->id]])
					->orderBy('event_specs.ordine', 'asc')->get();
					if(count($specs)>0){
						$template->cloneRow('specifica_w#'.$w, count($specs));
						foreach($specs as $spec){
							$template->setValue('specifica_w#'.$w.'#'.$i, $spec->label);
							$template->setValue('valore_w#'.$w.'#'.$i, EventSpec::getPrintableValue($spec->id_type, $spec->valore));
							$template->setValue('costo_w#'.$w.'#'.$i, $spec->costo);
							$importo_totale += $spec->costo;
							if($spec->costo == 0){
								$template->setValue('pagato_w#'.$w.'#'.$i, '');
							}else{
								if($spec->pagato == 1){
									$template->setValue('pagato_w#'.$w.'#'.$i, 'SI');
								}else{
									$template->setValue('pagato_w#'.$w.'#'.$i, 'NO');
								}
							}

							if($spec->pagato == false){
								$da_pagare += $spec->costo;
							}

							$i++;
						}
					}
					$w++;
				}
			}

			$template->setValue('importo_totale', number_format($importo_totale,2));
			$template->setValue('da_pagare', number_format($da_pagare,2));
		}




		//salvo il file docx/pdf nella temp
		$filename = "/subscription_".$input['id_subscription'];
		$path = sys_get_temp_dir().$filename.".docx";
		$output = sys_get_temp_dir();
		$template->saveAs($path);
		shell_exec("libreoffice --headless --convert-to pdf ".$path." --outdir ".$output);
		return response()->file($output.$filename.".pdf");
	}
}
