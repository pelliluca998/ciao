<?php
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use Modules\User\Entities\Group;
use App\SpecSubscription;
use Modules\User\Entities\User;
use App\CampoWeek;
use Modules\Subscription\Entities\Subscription;
use Modules\Oratorio\Entities\Oratorio;
use App\Classe;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Attributo\Entities\Attributo;
use Modules\Attributo\Entities\AttributoUser;
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/segresta-style.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
	<title>Report</title>


</head>
<?php
function stampa_tabella($input, $whereRaw, $format){
	$event = Event::findOrFail(Session::get('work_event'));
	$subs = DB::table('subscriptions as sub')
	->select('sub.id as id_subs', 'users.*',  'sub.type', 'sub.confirmed', 'users.id as id_user')
	->leftJoin('users', 'users.id', '=', 'sub.id_user')
	->whereRaw($whereRaw)
	->orderBy('users.cognome', 'asc')
	->orderBy('users.name', 'asc');

	$subs = $subs->get();
	echo "<table class='table table-bordered'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Utente</th>";
	echo "<th>Tipo</th>";
	echo "<th>Confermata</th>";

	if(count($input['spec_user'])>0){ //stampo l'intestazione delle colonne con specifiche utente
		foreach($input['spec_user'] as $column){
			echo "<th>".$column."</th>";
		}
	}

	if(count($input['spec'])>0){ //stampo l'intestazione delle colonne con specifiche iscrizione
		foreach($input['spec'] as $column){
			echo "<th>".EventSpec::find($column)->label."</th>";
		}
	}

	if(count($input['att_spec'])>0){
		foreach($input['att_spec'] as $fa){
			$a = Attributo::findOrfail($fa);
			echo "<th>".$a->nome."</th>";
		}
	}


	echo "</tr>";
	echo "</thead>";

	$tot_iscritti = 0;
	foreach($subs as $sub){
		$r=0;
		$filter_ok=true;
		/**
		** Dal composer ricevo un array di filter e filter_values grande quante tutte le specifiche.
		** Il filtro va applicato se il suo valore è maggiore di 0 (l'id della specifica da filtrare.)
		**/
		$filter_values = array_values($input['filter_value']);
		foreach($input['spec_order'] as $index){
			$id_filter = $input['filter'][$index];
			if($id_filter>0 && $filter_ok){
				$e = EventSpecValue::where([['id_eventspec', $id_filter], ['valore', $filter_values[$r]], ['id_subscription', $sub->id_subs] ])->get();
				if(count($e)==0){
					$filter_ok=false;
				}
			}

			$r++;

		}

		$r=0;
		if(count($input['att_filter'])>0){
			//$att_filter_values = array_values($input['att_filter_value']);
			$att_filter_values = $input['att_filter_value'];
			foreach($input['att_spec_order'] as $index){
				$id_filter = $input['att_filter'][$index];
				if($id_filter>0 && $filter_ok){
					$at = AttributoUser::where([['id_user', $sub->id_user], ['id_attributo', $id_filter], ['valore', $att_filter_values[$index]]])->get();
					if(count($at)==0) $filter_ok=false;
				}
				$r++;
			}
		}
		if($filter_ok){
			$tot_iscritti++;
			echo "<tr>";
			echo "<td>".$sub->id_subs."</td>";
			//controllo se stampare il nome in anagrafica o una delle specifiche indicate
			if($event->stampa_anagrafica==0){
				$array_specifiche = json_decode($event->spec_iscrizione);
				$anagrafica = EventSpecValue::where(['id_subscription' => $sub->id_subs])->whereIn('id_eventspec', $array_specifiche)->get();
				if(count($anagrafica)>0){
					echo "<td>";
					foreach($anagrafica as $a){
						echo $a->valore." ";
					}
					echo "</td>";
				}else{
					echo "<td><i style='font-size:12px;'>Specifica non esistente!</i></td>";
				}
			}else{
				echo "<td>".$sub->cognome." ".$sub->name."</td>";
			}
			//echo "<td>".$sub->cognome." ".$sub->name."</td>";
			echo "<td>".$sub->type."</td>";
			echo "<td>";
			if($sub->confirmed=='1'){
				if($format=='excel'){
					echo "SI";
				}else{
					echo "<i class='fa fa-check-square-o'></i> ";
				}
			}else{
				if($format=='excel'){
					echo "NO";
				}else{
					echo "<i class='fa fa-square-o'></i> ";
				}
			}
			echo "</td>";

			//SPECIFICHE UTENTE
			if(count($input['spec_user'])>0){
				foreach($input['spec_user'] as $user){
					echo "<td>".$sub->$user."</td>";
				}
			}
			//SPECIFICHE ISCRIZIONE
			if(count($input['spec'])>0){
				foreach($input['spec'] as $spec){
					$whereSpec = array('id_eventspec' => $spec, 'id_subscription' => $sub->id_subs);

					$value = EventSpecValue::leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')->where($whereSpec)->first();
					echo "<td>";
					if(isset($value->valore)){
						if($value->id_type>0){
							$val = TypeSelect::where('id', $value->valore)->get();
							if(count($val)>0){
								$val2 = $val[0];
								echo $val2->option;
							}else{
								echo "";
							}
						}else{
							switch($value->id_type){
								case -1:
								echo "<p>".$value->valore."</p>";
								break;
								case -2:
								if($value->valore==1){
									if($format=='excel'){
										echo "SI";
									}else{
										echo "<i class='fa fa-check-square-o fa-2x'></i> ";
									}
								}else{
									if($format=='excel'){
										echo "NO";
									}else{
										echo "<i class='fa fa-square-o fa-2x'></i> ";
									}
								}
								break;
								case -3:
								echo "<p>".$value->valore."</p>";
								break;

							}
						}

					}else{
						echo "n.d.";

					}
					echo "</td>";
				}
			}

			//ATTRIBUTI
			if(count($input['att_spec'])>0){
				foreach($input['att_spec'] as $at){
					$whereSpec = array('id_attributo' => $at, 'id_user' => $sub->id_user);

					$value = AttributoUser::leftJoin('attributos', 'attributos.id', '=', 'attributo_users.id_attributo')->where($whereSpec)->first();
					echo "<td>";
					if(isset($value->valore)){
						if($value->id_type>0){
							$val = TypeSelect::where('id', $value->valore)->get();
							if(count($val)>0){
								$val2 = $val[0];
								echo $val2->option;
							}else{
								echo "";
							}
						}else{
							switch($value->id_type){
								case -1:
								echo "<p>".$value->valore."</p>";
								break;
								case -2:
								if($value->valore==1){
									if($format=='excel'){
										echo "SI";
									}else{
										echo "<i class='fa fa-check-square-o fa-2x'></i> ";
									}
								}else{
									if($format=='excel'){
										echo "NO";
									}else{
										echo "<i class='fa fa-square-o fa-2x'></i> ";
									}
								}
								break;
								case -3:
								echo "<p>".$value->valore."</p>";
								break;

							}
						}

					}else{
						echo "n.d.";

					}
					echo "</td>";
				}
			}
			echo "</tr>";
		}
	}


	echo "</table>";
	echo "<p><b>Totale iscritti: $tot_iscritti</b></p>";
} ?>
<body>


	<div style="border:1; margin: 20px;">
		<?php
		//Controllo che l'array Input abbia tutti gli indici che mi aspetto, altrimenti lo inizializzo ad array vuoto.
		$keys = ['spec', 'spec_user', 'filter_value', 'filter', 'att_filter', 'att_filter_value', 'att_spec', 'user_filter'];
		foreach($keys as $key){
			if(!array_key_exists($key, $input)){
				$input[$key] = array();
			}
		}
		$event = Event::findOrFail(Session::get('work_event'));
		$oratorio = Oratorio::findOrFail($event->id_oratorio);
		?>
		<p style="text-align: center;">{!! $oratorio->nome !!}</p>
		<h2 style="text-align: center;">{!! $event->nome !!}</h2>
		<h3 style="text-align: center;">Report delle iscrizioni</h3>
		<?php
		$whereRaw = "sub.id_event = ".Session::get('work_event');
		$i=0;
		foreach($input['user_filter'] as $f){
			if($f=='1'){
				$whereRaw .= " AND users.".$input['user_filter_id'][$i]." LIKE '%".$input['user_filter_value'][$i]."%'";
			}
			$i++;
		}

		stampa_tabella($input, $whereRaw, $input['format']);

		?>
		<br>

	</div>
</body>
</html>
