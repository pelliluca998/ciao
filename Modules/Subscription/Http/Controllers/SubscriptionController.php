<?php

namespace Modules\Subscription\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Modules\Subscription\Entities\Subscription;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\Oratorio;
use App\SpecSubscription;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\Event;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\User\Entities\User;
use Modules\Contabilita\Entities\Bilancio;
use Modules\Contabilita\Entities\Cassa;
use Modules\Contabilita\Entities\ModoPagamento;
use Modules\Contabilita\Entities\TipoPagamento;
use Modules\Famiglia\Entities\Famiglia;
use Modules\Famiglia\Entities\ComponenteFamiglia;
use App\License;
use App\Comune;
use Modules\Event\Entities\Week;
use Module;
use Form;
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
use Yajra\DataTables\DataTables;
use Modules\Subscription\Http\Controllers\DataTables\SubscriptionDataTableEditor;
use Modules\Subscription\Http\Controllers\DataTables\SubscriptionDataTable;

class SubscriptionController extends Controller
{

	public function __construct(){
		$this->middleware('permission:view-iscrizioni')->only(['index', 'data']);
		$this->middleware('permission:edit-iscrizioni')->only(['action', 'store']);
	}

	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function event(Request $request){
		$input = $request->all();
		$id_event = $input['id_event'];
		Session::put('work_event', $id_event);
		return redirect()->route('subscription.index');
	}

	public function action(Request $request){
		$input = $request->all();
		if(!$request->has('check_user')){
			Session::flash("flash_message", "Devi selezionare almeno un utente!");
			return redirect()->route('subscription.index');
		}
		$json = $input['check_user'];
		//$json = json_encode($check_user);
		switch($input['action']){
			case 'approva':
			return redirect()->route('subscription.approve', ['check' => $json]);
			break;
			case 'cancella':
			return redirect()->route('subscription.batch_delete', ['check' => $json]);
			break;
		}
	}

	public function data(Request $request, Datatables $datatables){
		$input = $request->all();

		$builder = Subscription::query()
		->select('subscriptions.*', 'users.name', 'users.cognome')
		->leftJoin('users', 'subscriptions.id_user', 'users.id')
		->where('id_event', $input['id_event'])
		->orderBy('created_at', 'DESC');

		$event = Event::find($input['id_event']);

		return $datatables->eloquent($builder)
		->addColumn('action', function ($entity){
			$remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";
			$open = "<button class='btn btn-sm btn-primary btn-block' onclick='load_iscrizione(".$entity->id.")' type='button'><i class='fas fa-flag'></i> Apri</button>";
			$print = Form::open(['method' => 'GET', 'route' => ['subscription.print', $entity->id]])."<button class='btn btn-sm btn-primary btn-block'><i class='far fa-file-pdf'></i> Stampa</button>".Form::close();
			if(!Auth::user()->can('edit-iscrizioni')){
				$remove = "";
			}
			return $open.$print.$remove;
		})
		->addColumn('user_label', function ($sub) use ($event){
			if($event->stampa_anagrafica == 0){
				$array_specifiche = json_decode($event->spec_iscrizione);
				if($array_specifiche == null){
					return "<i style='font-size:12px;'>Specifica non esistente!</i>";
				}

				$anagrafica = EventSpecValue::select('event_spec_values.*', 'event_specs.label')
				->leftJoin('event_specs', 'event_specs.id', 'event_spec_values.id_eventspec')
				->where(['id_subscription' => $sub->id])->whereIn('id_eventspec', $array_specifiche)->get();
				if(count($anagrafica)>0){
					$val = "";
					foreach($anagrafica as $a){
						$val .= "<b>".$a->label.":</b> ".$a->valore."<br>";
					}
					return $val;
				}else{
					return "<i style='font-size:12px;'>Specifica non esistente!</i>";
				}
			}else{
				try{
					$user = User::findOrFail($sub->id_user);
					$val = "<div>".$user->full_name."<span id='sub_".$sub->id."' rel='popover'></span>"."</div>";
					//$val .= "<span id='sub_".$sub->id."' rel='popover'></span>";
					$content = "<b>Nome utente: </b>".$user->full_name."<br>";
					$content .= "<b>Data di nascita: </b>".$user->nato_il."<br>";
					return "<span id='sub_".$sub->id."' class='d-inline-block' data-html='true' data-placement='bottom' data-toggle='popover' data-trigger='hover' data-content='".$content."'>".$user->full_name."</span>";
				}catch(\Exception $e){
					return "Utente non esistente";
				}
			}
		})
		->filterColumn('user_label', function($query, $keyword) {
			$sql = "CONCAT(users.cognome,' ',users.name)  like ?";
			$query->whereRaw($sql, ["%{$keyword}%"]);
		})
		->addColumn('specs', function ($sub){
			$click = "load_spec_subscription(".$sub->id.")";
			$check = "<i style= \"color:#3e93c3; cursor: pointer;\" onclick=\"$click\" class='fa fa-flag fa-2x' aria-hidden='true'></i>";
			return $check;
		})
		->addColumn('DT_RowId', function ($entity){
			return $entity->id;
		})
		->rawColumns(['specs', 'action', 'user_label'])
		->toJson();
	}

	public function data_iscrizioni(Request $request, Datatables $datatables){
		$input = $request->all();

		$componente = ComponenteFamiglia::where('id_user', $input['id_user'])->first();

		$builder = Subscription::query()
		->select('subscriptions.*', 'users.name', 'users.cognome')
		->leftJoin('users', 'subscriptions.id_user', 'users.id');

		if($componente != null){
			$builder->whereIn('id_user', ComponenteFamiglia::where('id_famiglia', $componente->id_famiglia)->pluck('id_user')->toArray());
		}else{
			$builder->where('id_user', $input['id_user']);
		}

		$builder->orderBy('created_at', 'DESC');

		return $datatables->eloquent($builder)
		->addColumn('action', function ($entity){
			$remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";
			$open = "<button class='btn btn-sm btn-primary btn-block' onclick='load_iscrizione(".$entity->id.")' type='button'><i class='fas fa-flag'></i> Apri</button>";
			$print = Form::open(['method' => 'GET', 'route' => ['subscription.print', $entity->id]])."<button class='btn btn-sm btn-primary btn-block'><i class='far fa-file-pdf'></i> Stampa</button>".Form::close();
			if(!Auth::user()->can('edit-iscrizioni') || $entity->confirmed == 1){
				$remove = "";
			}
			return $open.$print.$remove;
		})
		->addColumn('user_label', function ($sub) {
			$event = Event::find($sub->id_event);
			if($event->stampa_anagrafica == 0){
				$array_specifiche = json_decode($event->spec_iscrizione);
				if($array_specifiche == null){
					return "<i style='font-size:12px;'>Specifica non esistente!</i>";
				}

				$anagrafica = EventSpecValue::select('event_spec_values.*', 'event_specs.label')
				->leftJoin('event_specs', 'event_specs.id', 'event_spec_values.id_eventspec')
				->where(['id_subscription' => $sub->id])->whereIn('id_eventspec', $array_specifiche)->get();
				if(count($anagrafica)>0){
					$val = "";
					foreach($anagrafica as $a){
						$val .= "<b>".$a->label.":</b> ".$a->valore."<br>";
					}
					return $val;
				}else{
					return "<i style='font-size:12px;'>Specifica non esistente!</i>";
				}
			}else{
				try{
					$user = User::findOrFail($sub->id_user);
					$val = "<div>".$user->full_name."<span id='sub_".$sub->id."' rel='popover'></span>"."</div>";
					//$val .= "<span id='sub_".$sub->id."' rel='popover'></span>";
					$content = "<b>Nome utente: </b>".$user->full_name."<br>";
					$content .= "<b>Data di nascita: </b>".$user->nato_il."<br>";
					return "<span id='sub_".$sub->id."' class='d-inline-block' data-html='true' data-placement='bottom' data-toggle='popover' data-trigger='hover' data-content='".$content."'>".$user->full_name."</span>";
				}catch(\Exception $e){
					return "Utente non esistente";
				}
			}
		})
		->filterColumn('user_label', function($query, $keyword) {
			$sql = "CONCAT(users.cognome,' ',users.name)  like ?";
			$query->whereRaw($sql, ["%{$keyword}%"]);
		})
		->addColumn('specs', function ($sub){
			$click = "load_spec_subscription(".$sub->id.")";
			$check = "<i style= \"color:#3e93c3; cursor: pointer;\" onclick=\"$click\" class='fa fa-flag fa-2x' aria-hidden='true'></i>";
			return $check;
		})
		->addColumn('DT_RowId', function ($entity){
			return $entity->id;
		})
		->addColumn('event_label', function ($entity){
			return Event::find($entity->id_event)->nome;
		})
		->rawColumns(['specs', 'action', 'user_label'])
		->toJson();
	}

	public function index_iscrizioni(SubscriptionDataTable $dataTable, Request $request){
		if($request->has('id_user')){
			return $dataTable->render('subscription::user')->withUser(User::find($input['id_user']));
		}

		return $dataTable->render('subscription::user')->withUser(Auth::user());

	}
	public function store_iscrizioni(SubscriptionDataTableEditor $editor){
		return $editor->process(request());
	}

	public function index(SubscriptionDataTable $dataTable, Request $request){
		$input = $request->all();
		if($request->has('id_event')){
			return $dataTable->render('subscription::index')->withEvent(Event::find($input['id_event']));
		}

		if(Session::has('work_event')){
			return $dataTable->render('subscription::index')->withEvent(Event::find(Session::get('work_event')));
		}else{
			Session::flash('flash_message', 'Per vedere le iscrizioni, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}
	}

	public function store(SubscriptionDataTableEditor $editor){
		return $editor->process(request());
	}

	public function contact(){
		return view('subscription::contact');
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
		return view('subscription::subscribe.subscribe', ['event' => $event, 'id_user' => $input['id_user']]);
	}

	public function savesubscribe(Request $request){

		if(!Session::has('id_subscription')){
			$input = $request->all();
			$sub = Subscription::create($input);
			//salvo le specifiche
			$specs = $input['specs'];
			$id_spec = $input['id_spec'];
			$costo = $input['costo'];
			$acconto = $input['acconto'];
			$pagato = $input['pagato'];
			$week = $input['id_week'];
			$i=0;
			foreach($specs as $spec){
				$e = new EventSpecValue;
				$e->id_eventspec=$id_spec[$i];
				$e->valore=$spec;
				$e->id_subscription = $sub->id;
				$e->id_week=$week[$i];
				$e->costo = floatval($costo[$i]);
				$e->acconto = floatval($acconto[$i]);
				$e->pagato = $pagato[$i];
				$e->save();

				//contabilita
				if(Module::find('contabilita')!=null && License::isValid('contabilita') && !Auth::user()->hasRole('user')){
					if($pagato[$i]==1){
						//pagamento avvenuto ora, salvo una riga in bilancio
						$id_cassa=0;
						$id_modo=0;
						$id_tipo=0;
						$user = User::findOrFail($input['id_user']);
						$event_spec = EventSpec::where('id', $e->id_eventspec)->first();
						if($event_spec->id_cassa != null){
							$id_cassa = $event_spec->id_cassa;
						}
						if($event_spec->id_modopagamento != null){
							$id_modo = $event_spec->id_modopagamento;
						}
						if($event_spec->id_tipopagamento != null){
							$id_tipo = $event_spec->id_tipopagamento;
						}

						//costo
						$bilancio = new Bilancio;
						$bilancio->id_event = $input['id_event'];
						$bilancio->id_admin = Auth::user()->id;
						$bilancio->id_user = $user->id;
						$bilancio->id_eventspecvalues = $e->id;
						$bilancio->id_tipopagamento = $id_tipo;
						$bilancio->id_modalita = $id_modo;
						$bilancio->id_cassa = $id_cassa;
						$bilancio->id_subscription = $sub->id;
						$bilancio->descrizione = "Incasso da iscrizione";
						$bilancio->importo = floatval($costo[$i]);
						$bilancio->data = date('d/m/Y');
						$bilancio->tipo_incasso = 1;
						$bilancio->save();

						//acconto
						// if(floatval($acconto[$i])>0){
						// 	$bilancio = new Bilancio;
						// 	$bilancio->id_event = $input['id_event'];
						// 	$bilancio->id_user = Auth::user()->id;
						// 	$bilancio->id_eventspecvalues = $e->id;
						// 	$bilancio->id_tipopagamento = $id_tipo;
						// 	$bilancio->id_modalita = $id_modo;
						// 	$bilancio->id_cassa = $id_cassa;
						// 	$user = User::findOrFail($input['id_user']);
						// 	$bilancio->descrizione = "Acconto da ".$user->cognome." ".$user->name." (iscrizione #".$sub->id.")";
						// 	$bilancio->importo = floatval($acconto[$i]);
						// 	$bilancio->data = date('Y-m-d');
						// 	$bilancio->tipo_incasso = 2;
						// 	$bilancio->save();
						// }
					}
				}
				//endcontabilita

				$i++;
			}
		}

		return redirect()->route('subscribe.grazie', ['id_subscription' => $sub->id]);
	}

	/*
	* Mostro la view con il messaggio finale e il button per il download del modulo
	*/

	public function grazie(Request $request, $id_subscription){
		$subscription = Subscription::find($id_subscription);
		if($subscription != null){
			return view('subscription::subscribe.grazie')->withSubscription($subscription)->withEvent(Event::find($subscription->id_event));
		}else{
			return redirect()->route('home');
		}

	}

	// public function savespecsubscribe(Request $request){
	// 	$input = $request->all();
	// 	//salvo le specifiche
	// 	if(isset($input['valore'])){
	// 		$valore = $input['valore'];
	// 		$id_eventspec = $input['id_eventspec'];
	// 		$id_week = $input['id_week'];
	// 		$costo = $input['costo_2'];
	// 		$acconto = $input['acconto_2'];
	// 		$pagato = $input['pagato_2'];
	// 		$user = User::findOrFail(Subscription::findOrFail($input['id_subscription'])->id_user);
	// 		$i=0;
	// 		foreach($valore as $valore){
	// 			$e = new EventSpecValue;
	// 			$e->id_eventspec=$id_eventspec[$i];
	// 			$e->valore=$valore;
	// 			$e->id_subscription = $input['id_subscription'];
	// 			$e->id_week = $id_week[$i];
	// 			$e->pagato = $pagato[$i];
	// 			$e->costo = $costo[$i];
	// 			$e->acconto = $acconto[$i];
	// 			$e->save();
	//
	// 			//contabilita
	// 			if(Module::find('contabilita')!=null && License::isValid('contabilita') && !Auth::user()->hasRole('user')){
	// 				if($pagato[$i]==1){
	// 					//pagamento avvenuto ora, salvo una riga in bilancio
	// 					$id_cassa=0;
	// 					$id_modo=0;
	// 					$id_tipo=0;
	// 					$event_spec = EventSpec::where('id', $e->id_eventspec)->first();
	// 					if($event_spec->id_cassa!=null){
	// 						$id_cassa = $event_spec->id_cassa;
	// 					}
	// 					if($event_spec->id_modopagamento!=null){
	// 						$id_modo = $event_spec->id_modopagamento;
	// 					}
	// 					if($event_spec->id_tipopagamento!=null){
	// 						$id_tipo = $event_spec->id_tipopagamento;
	// 					}
	// 					//costo
	// 					$bilancio = new Bilancio;
	// 					$bilancio->id_event = $input['id_event'];
	// 					$bilancio->id_user = Auth::user()->id;
	// 					$bilancio->id_eventspecvalues = $e->id;
	// 					$bilancio->id_tipopagamento = $id_tipo;
	// 					$bilancio->id_modalita = $id_modo;
	// 					$bilancio->id_cassa = $id_cassa;
	// 					$bilancio->descrizione = "Pagamento da ".$user->cognome." ".$user->name." (iscrizione #".$input['id_subscription'].")";
	// 					$bilancio->importo = floatval($costo[$i]);
	// 					$bilancio->data = date('Y-m-d');
	// 					$bilancio->tipo_incasso = 1;
	// 					$bilancio->save();
	//
	// 					//Acconto
	// 					if(floatval($acconto[$i])>0){
	// 						$bilancio = new Bilancio;
	// 						$bilancio->id_event = $input['id_event'];
	// 						$bilancio->id_user = Auth::user()->id;
	// 						$bilancio->id_eventspecvalues = $e->id;
	// 						$bilancio->id_tipopagamento = $id_tipo;
	// 						$bilancio->id_modalita = $id_modo;
	// 						$bilancio->id_cassa = $id_cassa;
	// 						$bilancio->descrizione = "Pagamento da ".$user->cognome." ".$user->name." (iscrizione #".$input['id_subscription'].")";
	// 						$bilancio->importo = floatval($acconto[$i]);
	// 						$bilancio->data = date('Y-m-d');
	// 						$bilancio->tipo_incasso = 2;
	// 						$bilancio->save();
	// 					}
	// 				}
	// 			}
	// 			//endcontabilita
	//
	// 			$i++;
	// 		}
	// 	}
	// 	return view('subscription::subscribe.grazie')->with('id_subscription', Session::get('id_subscription'));
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
			if($filter == null) $filter = array();
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
		switch($type){
			case "sms":
			return redirect()->route('sms.create', ['users' => $json]);
			break;
			case "email":
			return redirect()->route('email.create', ['users' => $json]);
			break;
			case "telegram":
			return redirect()->route('telegram.create', ['users' => $json]);
			break;
			case "whatsapp":
			return redirect()->route('whatsapp.create', ['users' => $json]);
			break;
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
			//return redirect()->route('subscription.index');
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un'iscrizione prima di approvarle!");
			//return redirect()->route('subscription.index');
		}

		return;


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
			//return redirect()->route('subscription.index');
		}else{
			Session::flash("flash_message", "Devi selezionare almeno un'iscrizione prima di cancellarle!");
			//return redirect()->route('subscription.index');
		}

		return;


	}

	public function print_subscription(Request $request, $id_subscription){
		$input = $request->all();
		//Se l'id_subscription è preview, allora devo stampare Anteprima del template senza i dati dell'iscrizione
		if($id_subscription == 'preview'){
			$event = Event::findOrFail(Session::get('work_event'));
			if($event->template_file == null){
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/template/subscription_template.docx')));
			}else{
				$template = new \PhpOffice\PhpWord\TemplateProcessor(url(Storage::url('public/'.$event->template_file)));
			}
			$oratorio = Oratorio::findOrFail($event->id_oratorio);
			$template->setValue('nome_oratorio', $oratorio->nome);
			$template->setValue('nome_parrocchia', $oratorio->nome_parrocchia);
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
					$acconti = json_decode($spec->acconto, true);
					if(isset($costi['0'])){
						$costo = $costi['0'];
					}else{
						$costo = 0;
					}
					if(isset($acconti['0'])){
						$acconto = $acconti['0'];
					}else{
						$acconto = 0;
					}
					$template->setValue('costo_g#'.$i, $costo."€");
					$template->setValue('acconto_g#'.$i, $acconto."€");
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
								$acconti = json_decode($spec->acconto, true);
								if(isset($acconti[$week->id])){
									$acconto = $acconti[$week->id];
								}else{
									$acconto = 0;
								}
								$template->setValue('costo_w#'.$w.'#'.$i, $costo."€");
								$template->setValue('acconto_w#'.$w.'#'.$i, $acconto."€");
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
			$sub = Subscription::findOrFail($id_subscription);
			$event = Event::findOrFail($sub->id_event);
			$oratorio = Oratorio::findOrFail($event->id_oratorio);
			//utente a cui è intestata l'iscrizione
			$user = User::findOrFail($sub->id_user);

			//cerco il padre
			if(Module::has('famiglia')){
				$padre = ComponenteFamiglia::getPadre($user->id);
				$madre = ComponenteFamiglia::getMadre($user->id);
			}else{
				$padre = "";
				$madre = "";
			}

			$storagePath  = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
			if($event->template_file == null){
				$template = new \PhpOffice\PhpWord\TemplateProcessor($storagePath."/template/subscription_template.docx");
			}else{
				$template = new \PhpOffice\PhpWord\TemplateProcessor($storagePath.$event->template_file);
			}

			//controllo se nel modulo devono essere stampati i dati anagrafici o, al loro posto, il valore di una specifica
			if($event->stampa_anagrafica == 1){
				$template->setValue('nominativo', '');
				$template->cloneBlock('dati_anagrafici');
			}else{
				$template->replaceBlock('dati_anagrafici', 'dati2');
				$array_specifiche = json_decode($event->spec_iscrizione);
				$anagrafica = EventSpecValue::where(['id_subscription' => $sub->id])->whereIn('id_eventspec', $array_specifiche)->get();
				if(count($anagrafica)>0){
					$nominativo = "";
					foreach($anagrafica as $a){
						$nominativo .= $a->valore." ";
					}
					$template->setValue('nominativo', $nominativo);
				}else{
					$template->setValue('nominativo', 'Valore non valido!');
				}
			}

			$template->setValue('nome_oratorio', $oratorio->nome);
			$template->setValue('email_oratorio', $oratorio->email);
			$template->setValue('nome_parrocchia', $oratorio->nome_parrocchia);
			$template->setValue('indirizzo_parrocchia', $oratorio->indirizzo_parrocchia);
			$template->setValue('nome_diocesi', $oratorio->nome_diocesi);
			$template->setValue('nome_evento', $event->nome);
			$template->setValue('id_subscription', $sub->id);
			$template->setValue('padre', $padre!=null?$padre->full_name:'');
			$template->setValue('madre', $madre!=null?$madre->full_name:'');
			$template->setValue('figlio', $user->full_name);
			$template->setValue('patologie', $user->patologie);
			$template->setValue('allergie', $user->patologie);
			$template->setValue('note', $user->note);
			$template->setValue('luogo_nascita', Comune::find($user->id_comune_nascita)->nome);
			$template->setValue('data_nascita', $user->nato_il);
			$template->setValue('comune_residenza', Comune::find($user->id_comune_residenza)->nome);
			$template->setValue('indirizzo', $user->via);
			$template->setValue('tessera_sanitaria', $user->tessera_sanitaria);
			$template->setValue('telefono', $user->telefono);
			$template->setValue('cellulare', $user->cellulare);
			$cell = ($padre != null)?$padre->cellulare:"";
			$cell .= ($cell == "" && $madre != null)?$madre->cellulare:"";
			$email = ($padre != null)?$padre->email:"";
			$email .= ($email == "" && $madre != null)?$madre->email:"";
			$template->setValue('cellulare_genitore', $cell);
			$template->setValue('email_genitore', $email);

			//specifiche generali
			$importo_totale = 0;
			$acconto_totale = 0;
			$da_pagare = 0;
			$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.acconto', 'event_spec_values.pagato')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->where([['event_spec_values.id_subscription', $sub->id], ['event_specs.general', 1]])
			->orderBy('event_specs.ordine', 'asc')->get();
			if(count($specs)>0){
				$template->cloneRow('specifica_g', count($specs));
				$i = 1;
				foreach($specs as $spec){
					$template->setValue('specifica_g#'.$i, $spec->label);
					$template->setValue('valore_g#'.$i, EventSpec::getPrintableValue($spec->id_type, $spec->valore));
					if(($spec->id_type==-2 && $spec->valore == 1) || $spec->id_type!=-2){
						$template->setValue('costo_g#'.$i, $spec->costo);
						$template->setValue('acconto_g#'.$i, $spec->acconto);
						$importo_totale += $spec->costo;
						$acconto_totale += $spec->acconto;
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
							$da_pagare += $spec->costo-$spec->acconto;
						}
					}else{
						$template->setValue('costo_g#'.$i, '');
						$template->setValue('acconto_g#'.$i, '');
						$template->setValue('pagato_g#'.$i, '');
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
					$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.acconto', 'event_spec_values.pagato', 'event_specs.valid_for')
					->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
					->where([['event_spec_values.id_subscription', $sub->id], ['event_specs.general', 0], ['event_spec_values.id_week', $week->id]])
					->orderBy('event_specs.ordine', 'asc')->get();
					if(count($specs)>0){
						$template->cloneRow('specifica_w#'.$w, count($specs));
						foreach($specs as $spec){
							$template->setValue('specifica_w#'.$w.'#'.$i, $spec->label);
							$template->setValue('valore_w#'.$w.'#'.$i, EventSpec::getPrintableValue($spec->id_type, $spec->valore));
							if(($spec->id_type==-2 && $spec->valore == 1) || $spec->id_type!=-2){
								$template->setValue('costo_w#'.$w.'#'.$i, $spec->costo);
								$template->setValue('acconto_w#'.$w.'#'.$i, $spec->acconto);
								$importo_totale += $spec->costo;
								$acconto_totale += $spec->acconto;
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
									$da_pagare += ($spec->costo-$spec->acconto);
								}
							}else{
								$template->setValue('pagato_w#'.$w.'#'.$i, '');
								$template->setValue('costo_w#'.$w.'#'.$i, '');
								$template->setValue('acconto_w#'.$w.'#'.$i, '');
							}


							$i++;
						}
					}
					$w++;
				}
			}else{
				$template->deleteBlock('settimana');
			}

			$template->setValue('importo_totale', number_format($importo_totale,2));
			$template->setValue('acconto_totale', number_format($acconto_totale,2));
			$template->setValue('da_pagare', number_format($da_pagare,2));
		}




		//salvo il file docx/pdf nella temp
		$filename = "/temp/subscription_".$id_subscription;
		$storagePath  = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
		if(!Storage::exists("public/temp")){
			Storage::makeDirectory("public/temp", 0755, true);
		}
		
		//$path = sys_get_temp_dir().$filename.".docx";
		$path = $storagePath.$filename.".docx";
		$output = $storagePath;
		$template->saveAs($path);

		//converto il file in pdf
		$exec = "unoconv -f pdf ".$path;
		shell_exec($exec);
		//stampo 1/2 pagine per foglio in base alle impostazioni
		$response_file = $output.$filename.".pdf";
		switch($event->pagine_foglio){
			case 2:
			$cmd = "pdfjam --nup 2x1 --landscape --a4paper --outfile ".$output."/".$filename."-2up.pdf ".$response_file;
			shell_exec($cmd);
			$response_file = $output.$filename."-2up.pdf";
			break;
		}
		return response()->file($response_file);
	}

	public function show_eventspecvalues($id_sub){
		$subscription = Subscription::find($id_sub);
		$event = Event::find($subscription->id_event);
		return view('subscription::eventspecvalue.show')->withSubscription($subscription)->withEvent($event);
	}

	public function show_iscrizione($id_sub){
		$subscription = Subscription::find($id_sub);
		$event = Event::find($subscription->id_event);
		return view('subscription::eventspecvalue.showuser')->withSubscription($subscription)->withEvent($event);
	}

	public function user_popover(Request $request){
		$input = $request->all();
		$subscription = Subscription::find($input['id_subscription']);
		$user = User::find($subscription->id_user);
		if($user == null){
			return "";
		}

		return "<b>Nome utente:</b> ".$user->full_name."<br><b>Data di nascita:</b>".$user->nato_il."<br>";
	}
}
