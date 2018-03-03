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
use Modules\Elenco\Entities\ElencoValue;
use App\TypeSelect;
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
<body>


<div style="border:1; margin: 20px;">
<?php
//Controllo che l'array Input abbia tutti gli indici che mi aspetto, altrimenti lo inizializzo ad array vuoto.
$keys = ['spec', 'spec_user', 'filter_id', 'filter_value', 'filter', 'colonna_filter', 'colonna_filter_id', 'colonna_filter_value', 'att_spec'];
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
<h3 style="text-align: center;">Report Elenco: {{$elenco->nome}}</h3>
<?php
function stampa_tabella($input, $select_value, $whereRaw, $columSpecs, $elenco){
	$values = ElencoValue::select('users.id as id_user', 'users.*', 'elenco_values.id', 'elenco_values.valore', 'subscriptions.id as id_subs')
			->leftJoin('users', 'users.id', 'elenco_values.id_user')
			->leftJoin('subscriptions', 'subscriptions.id_user', 'users.id')
			->where([['id_elenco', $elenco->id],['subscriptions.id_event', $elenco->id_event]])
			->whereRaw($whereRaw)
			->orderBy('users.cognome', 'ASC')
			->get();
	
	echo "<table class='testgrid'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Utente</th>";
	
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
		
	//intestazione colonne elenco
	$colonne = json_decode($elenco->colonne, true);
	$keys2 = array_keys($colonne);
	$loop=0;
	if(count($colonne)>0){
		foreach($colonne as $c){
			if(in_array($keys2[$loop], $input['colonna'])){
				echo "<th>".$c."</th>";
			}
			$loop++;
		}
	}
	
	echo "</tr>";
	echo "</thead>";
	
	$tot_iscritti = 0;
	foreach($values as $value){
		$filter_ok=true;
		$r=0;
		
		foreach($input['filter'] as $f){
			if($f==1 && $filter_ok){
				$e = EventSpecValue::where([['id_eventspec', $input['filter_id'][$r]], ['valore', $input['filter_value'][$r]], ['id_subscription', $value->id_subs] ])->get();
				if(count($e)==0) $filter_ok=false;
			}
			$r++;
		}
		
		//controllo il filtro sui valori dell'elenco
		$r=0;
		$val = json_decode($value->valore, true);
		$loop=0;
		$keys2 = array_keys($colonne);
		foreach($input['colonna_filter'] as $f){
			if($f==1 && $filter_ok){
				$check = 0;
				if(isset($val[$keys2[$loop]])){
					$check = $val[$keys2[$loop]];
				}
				if($input['colonna_filter_value'][$r]!=$check) $filter_ok=false;
			}	
		
			
			$loop++;
			$r++;
		}
		
		if($filter_ok){
			$tot_iscritti++;
			echo "<tr>";
			echo "<td>".$value->id."</td>";
			echo "<td>".$value->cognome." ".$value->name."</td>";
			
		//SPECIFICHE UTENTE
		if(count($input['spec_user'])>0){
			foreach($input['spec_user'] as $user){
				echo "<td>".$value->$user."</td>";
			}
		}
		//SPECIFICHE ISCRIZIONE
		if(count($input['spec'])>0){
			foreach($input['spec'] as $spec){
				$whereSpec = array('id_eventspec' => $spec, 'id_subscription' => $value->id_subs);

				$spec = EventSpecValue::leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')->where($whereSpec)->first();
				echo "<td>";
				if(isset($spec->valore)){
					if($spec->id_type>0){
						$val = TypeSelect::where('id', $spec->valore)->get();
						if(count($val)>0){
							$val2 = $val[0];
							echo $val2->option;
						}else{
							echo "";
						}
					}else{
						switch($spec->id_type){
							case -1:
								echo "<p>".$spec->valore."</p>";
								break;
							case -2:
								$icon = "<i class='fa ";
								if($spec->valore==1){
									$icon .= "fa-check-square-o";
								}else{
									$icon .= "fa-square-o";
								}
								$icon .= " fa-2x' aria-hidden='true'></i>";
								echo $icon;
								break;
							case -3:
								echo "<p>".$spec->valore."</p>";
								break;
							case -4:
								$val = Group::where('id', $spec->valore)->get();
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
		
		//VALORI ELENCO
		if(count($colonne)>0){
			$val = json_decode($value->valore, true);
			$loop=0;
			$keys2 = array_keys($colonne);
			foreach($colonne as $c){
				if(in_array($keys2[$loop], $input['colonna'])){
					$check = 0;
					if(isset($val[$keys2[$loop]])){
						$check = $val[$keys2[$loop]];
					}
					echo "<td>";
					$icon = "<i class='fa ";
					if($check==1){
						$icon .= "fa-check-square-o";
					}else{
						$icon .= "fa-square-o";
					}
					$icon .= " fa-2x' aria-hidden='true'></i>";
					echo $icon;
					echo "</td>";
				}
				$loop++;
			}
		}
		echo "</tr>";
		}
		
		
		
		
	}

	echo "</table>";
	echo "<br><p><b>Totale iscritti: $tot_iscritti</b></p>";
}


$whereRaw = "subscriptions.id_event = ".Session::get('work_event');
$i=0;
foreach($input['user_filter'] as $f){
	if($f=='1'){
		$whereRaw .= " AND users.".$input['user_filter_id'][$i]." LIKE '%".$input['user_filter_value'][$i]."%'";
	}
	$i++;
}

/*if($input['group']>0){
	$selects = TypeSelect::where('id_type', $input['group'])->orderBy('ordine', 'asc')->get();
	foreach($selects as $select){
		echo "<h4>".$select->option."</h4>";
		stampa_tabella($input, $select->id, $whereRaw, $columSpecs);
	}
}else{*/
	stampa_tabella($input, 0,$whereRaw, $columSpecs, $elenco);
//}

	?>
<br>

</div>
</body>
</html>

