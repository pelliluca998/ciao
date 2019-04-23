<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Requests;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use Modules\Event\Entities\Event;
use Modules\Subscription\Entities\Subscription;
use Modules\Contabilita\Entities\Bilancio;
use Modules\User\Entities\User;
use Modules\Contabilita\Entities\Cassa;
use Modules\Contabilita\Entities\ModoPagamento;
use Modules\Contabilita\Entities\TipoPagamento;
use Module;
use Input;
use Session;
use Auth;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Oratorio\Entities\Type;
use Modules\Event\Entities\Week;
use Yajra\DataTables\DataTables;
use Modules\Event\Http\Controllers\DataTables\EventSpecValueDataTableEditor;
use Modules\Event\Http\Controllers\DataTables\EventSpecValueDataTable;

class EventSpecValueController extends Controller
{

	public function __construct(){
    $this->middleware('permission:edit-iscrizioni')->only(['store']);
  }

	public function index(EventSpecValueDataTable $dataTable){
		//return $dataTable->render('eventspecvalues::index');
	}

	public function store(EventSpecValueDataTableEditor $editor){
		return $editor->process(request());
	}

	public function data(Request $request, Datatables $datatables){
		$input = $request->all();

		$subscription = Subscription::find($input['id_iscrizione']);

		if(intval($input['id_week']) == 0){
			$builder = EventSpecValue::query()
			->select('event_spec_values.id_eventspec', 'event_specs.label as specifica_label', 'event_specs.id_type as id_type',
			'event_spec_values.acconto', 'event_spec_values.valore', 'event_spec_values.id', 'event_spec_values.costo', 'event_spec_values.pagato')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->where([
				['event_spec_values.id_subscription', $input['id_iscrizione']],
				['event_specs.general', 1],
			])
			->orderBy('event_specs.ordine', 'asc');
		}else{
			$builder = EventSpecValue::query()
			->select('event_spec_values.id_eventspec', 'event_specs.label as specifica_label', 'event_specs.id_type as id_type',
			'event_spec_values.acconto', 'event_spec_values.valore', 'event_spec_values.id', 'event_spec_values.costo', 'event_spec_values.pagato', 'event_specs.valid_for')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->where([
				['event_spec_values.id_subscription', $input['id_iscrizione']],
				['event_specs.general', 0],
				['event_spec_values.id_week', $input['id_week']],
				['event_specs.valid_for', "LIKE", '%"'.$input['id_week'].'":"1"%']
			])
			->orderBy('event_specs.ordine', 'asc');
		}



		return $datatables->eloquent($builder)
		->addColumn('action', function ($entity) use ($subscription){
			$edit = "<div style=''><div style='display: flow-root'><button class='btn btn-sm btn-primary btn-block' id='editor_edit' style='float: left; width: 50%; margin-right: 2px;'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";

			if(!Auth::user()->can('edit-iscrizioni') || $subscription->confirmed == 1){
				$remove = "";
			}

			return $remove;
		})
		->addColumn('DT_RowId', function ($entity){
			return $entity->id;
		})
		->addColumn('valore_label', function ($entity){
			if($entity->id_type > 0){
        $option = TypeSelect::find($entity->valore);
        return $option!=null?$option->option:"";
      }

      switch ($entity->id_type) {
        case Type::TEXT_TYPE:
        return $entity->valore;

        case Type::BOOL_TYPE:
        if($entity->valore == 1){
          return "<i class='far fa-check-circle fa-2x'></i>";
        }else{
          return "<i class='far fa-circle fa-2x'></i>";
        }

        case Type::NUMBER_TYPE:
        return $entity->valore;

        case Type::DATE_TYPE:
        return $entity->valore;
      }
		})
		->rawColumns(['action', 'valore_label'])
		->toJson();
	}

	public function valore_field(Request $request){
    $input = $request->all();
    $eventspecvalue = EventSpecValue::find($input['id_eventspecvalue']);
    if($eventspecvalue == null){
      return json_encode($input['id_eventspecvalue']);
    }

		$event_spec = EventSpec::find($eventspecvalue->id_eventspec);

    if($event_spec->id_type > 0){
      $types = TypeSelect::where('id_type', $event_spec->id_type)->orderBy('ordine', 'ASC')->pluck('id', 'option');
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'select', 'options' => $types));
    }

    switch ($event_spec->id_type) {
      case Type::TEXT_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'text'));

      case Type::BOOL_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'checkbox', 'options' => array('' => 1), 'separator' => '', 'unselectedValue' => '0'));

      case Type::NUMBER_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'attr' => array('type' => 'number')));

      case Type::DATE_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'datetime', 'format' => 'DD/MM/YYYY'));
    }
  }

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	// public function destroy($id){
	// 	$value = EventSpecValue::findOrFail($id);
	// 	$eventSpec = EventSpec::findOrFail($value->id_eventspec);
	// 	$event = Event::findOrFail($eventSpec->id_event);
	// 	$subscription = Subscription::findOrFail($value->id_subscription);
	// 	if($subscription->confirmed==1){
	// 		Session::flash("flash_message", "Impossibile eliminare la specifica selezionata, l'iscrizione è già confermata!");
	// 	}else{
	// 		if(Auth::user()->hasRole('admin')){
	// 			if($event->id_oratorio==Session::get('session_oratorio')){
	// 				$value->delete();
	// 				//delete riga da bilancio
	// 				if(Module::find('contabilita')!=null){
	// 					$bilancio = Bilancio::where('id_eventspecvalues', $id)->get();
	// 					if(count($bilancio)>0){
	// 						foreach($bilancio as $b){
	// 							$b->delete();
	// 						}
	// 					}
	// 				}
	// 				Session::flash("flash_message", "Specifica $id cancellata!");
	// 			}else{
	// 				abort(403, 'Unauthorized action.');
	// 			}
	// 		}elseif(Auth::user()->hasRole('user')){
	// 			if($subscription->id_user == Auth::user()->id){
	// 				$value->delete();
	// 				Session::flash("flash_message", "Specifica $id cancellata!");
	// 			}else{
	// 				abort(403, 'Unauthorized action.');
	// 			}
	// 		}
	// 	}
  //
	// 	$query = Session::get('query_param');
	// 	Session::forget('query_param');
	// 	if(Auth::user()->hasRole('admin')){
	// 		return redirect()->route('subscription.index', $query);
	// 	}else{
	// 		return redirect()->route('usersubscriptions.show');
	// 	}
  //
	// }


	// public function save(Request $request){
	// 	$input = $request->all();
	// 	$valore = $input['valore'];
	// 	$costo = $input['costo'];
	// 	$acconto = $input['acconto'];
	// 	$pagato = $input['pagato'];
	// 	$id_eventspec = Input::get('id_eventspec');
	// 	$id_eventspecvalue = Input::get('id_eventspecvalue');
	// 	$id_subscription = Input::get('id_subscription');
	// 	$id_week = Input::get('id_week');
	// 	$i=0;
	// 	ksort($valore);
	// 	//var_dump($valore);
	// 	$subscription = Subscription::findOrFail($id_subscription[0]);
	// 	$user = User::findOrFail($subscription->id_user);
	// 	foreach($valore as $value) {
	// 		$old_pagato = 0;
	// 		$old_acconto = 0;
	// 		if($id_eventspecvalue[$i]>0){
	// 			//update
	// 			$spec = EventSpecValue::findOrFail($id_eventspecvalue[$i]);
	// 			$old_pagato = $spec->pagato;
	// 			$spec->valore = $value;
	// 			$spec->costo = floatval($costo[$i]);
	// 			$spec->pagato = $pagato[$i];
	// 			if($pagato[$i]==0){
	// 				$old_acconto = $spec->acconto;
	// 				$spec->acconto = floatval($acconto[$i]);
	// 			}
	// 			$spec->save();
  //
	// 		}else{
	// 			$spec = new EventSpecValue;
	// 			$spec->id_eventspec = $id_eventspec[$i];
	// 			$spec->valore = $value;
	// 			$spec->id_subscription = $id_subscription[$i];
	// 			$spec->id_week = $id_week[$i];
	// 			$spec->costo = floatval($costo[$i]);
	// 			$spec->pagato = $pagato[$i];
	// 			if($pagato[$i]==0){
	// 				$old_acconto = 0;
	// 				$spec->acconto = floatval($acconto[$i]);
	// 			}
	// 			$spec->save();
	// 		}
	// 		//contabilita
	// 		if(Module::find('contabilita')!=null && License::isValid('contabilita')){
	// 			//GESTIONE PAGAMENTO
	// 			$id_cassa=0;
	// 			$id_modo=0;
	// 			$id_tipo=0;
	// 			$event_spec = EventSpec::where('id', $spec->id_eventspec)->first();
	// 			if($event_spec->id_cassa!=null){
	// 				$id_cassa = $event_spec->id_cassa;
	// 			}
	// 			if($event_spec->id_modopagamento!=null){
	// 				$id_modo = $event_spec->id_modopagamento;
	// 			}
	// 			if($event_spec->id_tipopagamento!=null){
	// 				$id_tipo = $event_spec->id_tipopagamento;
	// 			}
	// 			if($old_pagato==0 && $pagato[$i]==1){
	// 				//pagamento avvenuto ora, salvo una riga in bilancio
	// 				//l'importo da registare è diverso se è già stato dato l'acconto
	// 				$importo = floatval($costo[$i]) - $spec->acconto;
	// 				$bilancio = new Bilancio;
	// 				$bilancio->id_event = Session::get('work_event');
	// 				$bilancio->id_user = Auth::user()->id;
	// 				$bilancio->id_eventspecvalues = $spec->id;
	// 				$bilancio->id_tipopagamento = $id_tipo;
	// 				$bilancio->id_modalita = $id_modo;
	// 				$bilancio->id_cassa = $id_cassa;
	// 				$bilancio->descrizione = "Pagamento da ".$user->full_name." (iscrizione #".$subscription->id.")";
	// 				$bilancio->importo = $importo;
	// 				$bilancio->tipo_incasso = 1;
	// 				$bilancio->data = date('Y-m-d');
	// 				$bilancio->save();
	// 			}elseif($old_pagato==1 && $pagato[$i]==0){
	// 				//elimino la riga corrispondente in bilancio
	// 				$bilancio = Bilancio::where([['id_eventspecvalues', $spec->id], ['tipo_incasso', 1]])->first();
	// 				if($bilancio!=null){
	// 					$bilancio->delete();
	// 				}
	// 			}
	// 			//END PAGAMENTO
	// 			//GESTIONE ACCONTO
	// 			if($pagato[$i]==0){
	// 				$bilancio_acconto = Bilancio::where([['id_eventspecvalues', $spec->id], ['tipo_incasso', 2]])->orderBy('created_at', 'DESC')->first();
	// 				//se non ho nessuna voce a bilancio di tipo acconto relativa a questa specifica, la creo solo se acconto!=0
	// 				if($bilancio_acconto == null && floatval($acconto[$i]) != 0){
	// 					$bilancio = new Bilancio;
	// 					$bilancio->id_event = Session::get('work_event');
	// 					$bilancio->id_user = Auth::user()->id;
	// 					$bilancio->id_eventspecvalues = $spec->id;
	// 					$bilancio->id_tipopagamento = $id_tipo;
	// 					$bilancio->id_modalita = $id_modo;
	// 					$bilancio->id_cassa = $id_cassa;
	// 					$bilancio->descrizione = "Acconto da ".$user->full_name." (iscrizione #".$subscription->id.")";
	// 					$bilancio->importo = floatval($acconto[$i]);
	// 					$bilancio->tipo_incasso = 2;
	// 					$bilancio->data = date('Y-m-d');
	// 					$bilancio->save();
	// 				}
  //
	// 				if($bilancio_acconto != null){
	// 					//voce di acconto già esistente, ne creo una nuova con l'importo modificato
	// 					$nuovo_importo = floatval($acconto[$i])-$old_acconto;
	// 					if($nuovo_importo!=0){
	// 						$bilancio = new Bilancio;
	// 						$bilancio->id_event = Session::get('work_event');
	// 						$bilancio->id_user = Auth::user()->id;
	// 						$bilancio->id_eventspecvalues = $spec->id;
	// 						$bilancio->id_tipopagamento = $id_tipo;
	// 						$bilancio->id_modalita = $id_modo;
	// 						$bilancio->id_cassa = $id_cassa;
	// 						$bilancio->descrizione = "Acconto da ".$user->full_name." (iscrizione #".$subscription->id.") aggiornato (valore precedente: ".$old_acconto." €)";
	// 						$bilancio->importo = $nuovo_importo;
	// 						$bilancio->tipo_incasso = 2;
	// 						$bilancio->data = date('Y-m-d');
	// 						$bilancio->save();
	// 					}
	// 				}
	// 			}
	// 			//END ACCONTO
	// 		}
	// 		//endcontabilita
	// 		$i++;
	// 	}
	// 	Session::flash("flash_message", "Dettagli salvati!");
	// 	$query = Session::get('query_param');
	// 	Session::forget('query_param');
	// 	if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner')){
	// 		return redirect()->route('subscription.index', $query);
	// 	}else{
	// 		return redirect()->route('usersubscriptions.show');
	// 	}
	// }

	public function riempi_specifica(Request $request){
		//echo "Riempimento specifica<br>";
		$id_eventspec = $request['id_eventspec'];
		$id_pesco = $request['id_pesco'];
		$valori_validi = $request['valore2'];
		$valori_validi_specifica = $request['valore1'];
		$event_spec = EventSpec::findOrFail($id_eventspec);
		$spec_vincolo = EventSpec::findOrFail($id_pesco);

		if($spec_vincolo->id_type == -2 && $valori_validi == null){
			$valori_validi = array("0");
		}

		echo "Specifica da riempire: ".$event_spec->label."<br>";
		echo "<br>Da dove pesco i valori: faccio scegliere una specifica e calcolo gli utenti totali per quella sepcifica. In questo caso scelgo solo gli utenti che nella specifica 'Classe frequentata' hanno un valore tra 'Prima elementare' e 'Quarta elementare'";

		//$valori_validi = array(106,107,108,109);
		//quante iscrizioni hanno queste caratteristiche
		$values = EventSpecValue::select('id_subscription')->where('id_eventspec', $id_pesco)->whereIn('valore', $valori_validi)->get()->toArray();
		$subscriptions = Subscription::select('id')->whereIn('id', $values)->get()->toArray();
		$num_sub = count($subscriptions);
		echo "<br>In totale ho $num_sub iscrizioni corrispondenti alle indicazioni<br>";

		//in quanti gruppi devo dividerli?
		$gruppi = TypeSelect::where('id_type', $event_spec->id_type)->whereIn('id', $valori_validi_specifica)->get();
		$num_gruppi = count($gruppi);
		$per_gruppo = intval($num_sub/$num_gruppi);
		echo "Numero gruppi in cui suddividerli: $num_gruppi, ovvero $per_gruppo per gruppo CIRCA";

		//divido
		$pescati = array();
		$gruppi_generati = array();
		foreach($gruppi as $gruppo){
			$g = array();
			echo "<h2>Gruppo ".$gruppo->option."</h2>";
			//ne pesco per_gruppo;
			$v = Subscription::whereIn('id', $subscriptions)->whereNotIn('id', $pescati)->take($per_gruppo)->inRandomOrder()->get();
			foreach($v as $spec){
				array_push($pescati, $spec->id);
				array_push($g, $spec->id);
				//echo "---- Aggiunta iscrizione ".$spec->id."<br>";
			}

			array_push($gruppi_generati, $g);
			var_dump($g);

		}

		$avanzo = $num_sub-count($pescati);
		//echo "<br><br>Se ho fatto bene i conti, avanzano $avanzo iscrizioni... Ridistribuisco!<br>";

		for($i=0; $i<$avanzo; $i++){
			//prendo un'iscrizione alla volta
			$s = Subscription::whereIn('id', $subscriptions)->whereNotIn('id', $pescati)->take($per_gruppo)->limit(1)->first();
			echo "<br>Iscrizione pescata: ".$s->id."<br>";
			array_push($pescati, $s->id);
			$index_array = rand(0, count($gruppi_generati)-1);
			array_push($gruppi_generati[$index_array], $s->id);
		}

		//creo le specifiche
		$i = 0;
		foreach($gruppi as $gruppo){

			foreach($gruppi_generati[$i] as $subs){
				$spec = new EventSpecValue;
				$spec->id_eventspec = $id_eventspec;
				$spec->id_subscription = $subs;
				$spec->valore = $gruppo->id;
				$spec->id_week = 0;
				$spec->costo = 0;
				$spec->pagato = 0;
				$spec->save();

			}
			$i++;
		}

		Session::flash('flash_message', 'Squadre generate!');
		return redirect()->route('subscription.index');

		//echo "<br><br><h1>Gruppi defintivi:</h1>";
		//var_dump($gruppi_generati);
	}

	public function elimina_specifica(Request $request){
		EventSpecValue::where('id_eventspec', $request['id_eventspec'])->delete();
		Session::flash('flash_message', 'Specifica eliminata da tutte le iscrizioni!');
		return redirect()->route('subscription.index');
	}

	public function aggiungi_specifica(Request $request){
		$input = $request->all();
		$event_spec = EventSpec::find($input['id_eventspec']);
		foreach(Subscription::where('id_event', Session::get('work_event'))->get() as $sub){
			$spec = new EventSpecValue;
			$spec->id_eventspec = $event_spec->id;
			$spec->id_subscription = $sub->id;
			$spec->valore = $input['valore'];
			$spec->id_week = $input['id_week'];
			$spec->costo = $input['costo'];
			$spec->acconto = $input['acconto'];
			$spec->pagato = false;
			$spec->save();
		}
		Session::flash('flash_message', 'Specifica aggiunta a tutte le iscrizioni!');
		return redirect()->route('subscription.index');
	}
}
