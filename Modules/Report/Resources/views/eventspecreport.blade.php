<?php
use App\Week;
use App\Event;
use App\Group;
use App\SpecSubscription;
use App\User;
use App\CampoWeek;
use App\Subscription;
use App\Oratorio;
use App\Classe;
use App\EventSpecValue;
use App\EventSpec;
use App\TypeSelect;
use App\Attributo;
use App\AttributoUser;
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
<body>


<div style="border:1; margin: 20px;">
<?php
//Controllo che l'array Input abbia tutti gli indici che mi aspetto, altrimenti lo inizializzo ad array vuoto.
$keys = ['spec', 'spec_user', 'filter_id', 'filter_value', 'filter', 'att_filter', 'att_filter_id', 'att_filter_value', 'att_spec'];
foreach($keys as $key){
	if(!array_key_exists($key, $input)){			
		$input[$key] = array();
	}
}
$event = Event::findOrFail(Session::get('work_event'));
$oratorio = Oratorio::findOrFail($event->id_oratorio);
$columSpecs = array();
if(count($input['spec'])>0){
	$columSpecs = EventSpec::whereIn('id', $input['spec'])->orderBy('label', 'asc')->get();
}
?>
<p style="text-align: center;">{!! $oratorio->nome !!}</p>
<h2 style="text-align: center;">{!! $event->nome !!}</h2>
<h3 style="text-align: center;">Report delle iscrizioni</h3>
<?php
function stampa_tabella($input, $select_value, $whereRaw, $columSpecs){
	$event = Event::findOrFail(Session::get('work_event'));
	if($select_value>0){
		$subs = DB::table('subscriptions as sub')
			->select('sub.id as id_subs', 'users.*', 'sub.type', 'sub.confirmed', 'users.id as id_user')
			->leftJoin('users', 'users.id', '=', 'sub.id_user')
			->leftJoin('event_spec_values', 'event_spec_values.id_subscription', '=', 'sub.id')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->whereRaw('event_spec_values.valore = '.$select_value.' AND '.$whereRaw)
			->orderBy('users.cognome', 'asc')
			->orderBy('users.name', 'asc');

	}else{
		$subs = DB::table('subscriptions as sub')
			->select('sub.id as id_subs', 'users.*',  'sub.type', 'sub.confirmed', 'users.id as id_user')
			->leftJoin('users', 'users.id', '=', 'sub.id_user')
			->whereRaw($whereRaw)
			->orderBy('users.cognome', 'asc')
			->orderBy('users.name', 'asc');
	}
	
	$subs = $subs->get();
	echo "<table class='testgrid'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Utente</th>";
	echo "<th>Tipo</th>";
	echo "<th>OK?</th>";

	if(count($input['spec_user'])>0){ //stampo l'intestazione delle colonne con specifiche utente
		foreach($input['spec_user'] as $column){
			echo "<th>".$column."</th>";
		}
	}

	if(count($columSpecs)>0){ //stampo l'intestazione delle colonne con specifiche iscrizione
		foreach($columSpecs as $column){
			echo "<th>".$column->label."</th>";
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
		foreach($input['filter'] as $f){
			if($f==1 && $filter_ok){
				$e = EventSpecValue::where([['id_eventspec', $input['filter_id'][$r]], ['valore', $input['filter_value'][$r]], ['id_subscription', $sub->id_subs] ])->get();
				if(count($e)==0) $filter_ok=false;
				}
			$r++;
		}

		$r=0;
		if(count($input['att_filter'])>0){
			foreach($input['att_filter'] as $fa){
				if($fa==1 && $filter_ok){
					$at = AttributoUser::where([['id_user', $sub->id_user], ['id_attributo', $input['att_filter_id'][$r]], ['valore', $input['att_filter_value'][$r]]])->get();
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
				$anagrafica = EventSpecValue::where([['id_eventspec', $event->spec_iscrizione], ['id_subscription', $sub->id_subs]])->get();
				if(count($anagrafica)>0){
					echo "<td>".$anagrafica[0]->valore."</td>";
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
				echo "<i class='fa fa-check-square-o' aria-hidden='true'></i> ";
			}else{
				echo "<i class='fa fa-square-o' aria-hidden='true'></i>";
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
									$icon = "<i class='fa ";
									if($value->valore==1){
										$icon .= "fa-check-square-o";
									}else{
										$icon .= "fa-square-o";
									}
									$icon .= " fa-2x' aria-hidden='true'></i>";
									echo $icon;
									break;
								case -3:
									echo "<p>".$value->valore."</p>";
									break;
								case -4:
									$val = Group::where('id', $value->valore)->get();
									if(count($val)>0){
										$val2 = $val[0];
										echo $val2->nome;
									}else{
										echo "";
									}
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
									$icon = "<i class='fa ";
									if($value->valore==1){
										$icon .= "fa-check-square-o";
									}else{
										$icon .= "fa-square-o";
									}
									$icon .= " fa-2x' aria-hidden='true'></i>";
									echo $icon;
									break;
								case -3:
									echo "<p>".$value->valore."</p>";
									break;
								case -4:
									$val = Group::where('id', $value->valore)->get();
									if(count($val)>0){
										$val2 = $val[0];
										echo $val2->nome;
									}else{
										echo "";
									}
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
	}


$whereRaw = "sub.id_event = ".Session::get('work_event');
$i=0;
foreach($input['user_filter'] as $f){
	if($f=='1'){
		$whereRaw .= " AND users.".$input['user_filter_id'][$i]." LIKE '%".$input['user_filter_value'][$i]."%'";
	}
	$i++;
}

if($input['group']>0){
	$selects = TypeSelect::where('id_type', $input['group'])->orderBy('ordine', 'asc')->get();
	foreach($selects as $select){
		echo "<h4>".$select->option."</h4>";
		stampa_tabella($input, $select->id, $whereRaw, $columSpecs);
	}
}else{
	stampa_tabella($input, 0,$whereRaw, $columSpecs);
}

	?>
<br>

</div>
</body>
</html>

