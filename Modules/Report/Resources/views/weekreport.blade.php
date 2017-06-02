<?php
use App\Week;
use App\Event;
use App\Campo;
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

$event = Event::findOrFail(Session::get('work_event'));
$oratorio = Oratorio::findOrFail($event->id_oratorio);
?>
<p style="text-align: center;">{!! $oratorio->nome !!}</p>
<h2 style="text-align: center;">{!! $event->nome !!}</h2>
<h3 style="text-align: center;">Report delle iscrizioni</h3>
<?php
function stampa_tabella($input){
	$event = Event::findOrFail(Session::get('work_event'));
	$weeks = Week::where('id_event', Session::get('work_event'))->orderBy('from_date', 'ASC')->get();
	$w=0;
	foreach($weeks as $week){
		//controllo che per la settimana corrente sia stato scelto qualche campo da stampare...
		if(isset($input['week'][$w])){
			echo "<h2>Settimana dal ".$week->from_date." al ".$week->to_date."</h2>";			
			echo "<table class='testgrid'>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Utente</th>";
			//intestazione campi da inserire nel report
			//$columCampi = Campo::select('campos.label', 'campos.id', 'campos.id_type as id_type')->whereIn('campos.id', $input['campo'][$w])->orderBy('campos.id', 'asc')->get();
			$columnSpecs1 = (new EventSpec)
				->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for')
				->whereIn('event_specs.id', $input['week'][$w])
				->get();
			foreach($columnSpecs1 as $c){
				echo "<th>".$c->label."</th>";
			}

			//inserimento colonne da specifiche 1
			$columnSpecs2 = EventSpec::whereIn('id', $input['spec'])->orderBy('id', 'asc')->get();
			foreach($columnSpecs2 as $c){
				echo "<th>".$c->label."</th>";
			}
			echo "</tr>";
			//inserimento dati
 			$subs = Subscription::select('subscriptions.id', 'users.name', 'users.cognome')
 				->leftJoin('users', 'users.id', 'subscriptions.id_user')
 				->whereIn('subscriptions.id', 
 					EventSpecValue::select('event_spec_values.id_subscription')
 					->where('event_spec_values.id_week', $week->id)
 					->get()
 					->toArray())
 				->get();
 				$tot_iscritti = 0;
			foreach($subs as $sub){				
				/** Prima di stampare la riga, controllo quali campi hanno il flag Filtra==1
				Per ogni filtro, eseguo una query e verifico se corrisponde al valore del filtro impostato
				**/
				$filter_ok=true;
				$filters = $input['week_filter'][$w];
				$filter_id = $input['week_filter_id'][$w];
				$filter_values = $input['week_filter_value'][$w];
				$f=0;
				foreach($filters as $filter){
					if($filter==1 && $filter_ok){
						$specs = EventSpecValue::where([['id_subscription', $sub->id],['id_week', $week->id], ['id_eventspec', $filter_id[$f]], ['valore', $filter_values[$f]]])->orderBy('id_eventspec')->get();
						if(count($specs)==0) $filter_ok=false;
					}
					$f++;
				}
				
				//filtro sulle specs1
				$f=0;
				foreach($input['spec_filter'] as $filter){
					if($filter==1 && $filter_ok){
						$specs = EventSpecValue::where([['id_subscription', $sub->id], ['id_eventspec', $input['spec_filter_id'][$f]], ['valore', $input['spec_filter_value'][$f]]])->orderBy('id_eventspec')->get();
						if(count($specs)==0) $filter_ok=false;
					}
					$f++;
				}
            		if($filter_ok){
            			$tot_iscritti++;
					echo "<tr>";
					echo "<td>".$sub->id."</td>";
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
					//echo "<td>".$sub->cognome."</td>";
					//get valore dei campi
					foreach($columnSpecs1 as $c){
						$specs = EventSpecValue::where([['id_subscription', $sub->id],['id_week', $week->id], ['id_eventspec', $c->id]])->orderBy('id_eventspec')->first();
						echo "<td>";
						if(count($specs)!=0){
							if($c->id_type>0){
								$val = TypeSelect::where('id', $specs->valore)->get();
								if(count($val)>0){
									$val2 = $val[0];
									echo $val2->option;
								}else{
									echo "";
								}
							}else{
								switch($c->id_type){
									case -1:
										echo "<p>".$specs->valore."</p>";
										break;
									case -2:
										$icon = "<i class='fa ";
										if($specs->valore==1){
											$icon .= "fa-check-square-o";
										}else{
											$icon .= "fa-square-o";
										}
										$icon .= " fa-2x' aria-hidden='true'></i>";
										echo $icon;
										break;
									case -3:
										echo "<p>".$specs->valore."</p>";
										break;
									case -4:
										$val = Group::where('id', $specs->valore)->get();
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
                		
                		//valori specs1
                		$specs = EventSpecValue::select('event_specs.id_type', 'event_spec_values.valore')->leftJoin('event_specs', 'event_specs.id', 'event_spec_values.id_eventspec')->where('event_spec_values.id_subscription', $sub->id)->whereIn('event_spec_values.id_eventspec', $input['spec'])->orderBy('event_spec_values.id_eventspec', 'asc')->get();
                		foreach($specs as $c){
						echo "<td>";
						if($c->id_type>0){
							$val = TypeSelect::where('id', $c->valore)->get();
							if(count($val)>0){
								$val2 = $val[0];
								echo $val2->option;
							}else{
								echo "";
							}
						}else{
							switch($c->id_type){
								case -1:
									echo "<p>".$c->valore."</p>";
									break;
								case -2:
									$icon = "<i class='fa ";
									if($c->valore==1){
										$icon .= "fa-check-square-o";
									}else{
										$icon .= "fa-square-o";
									}
									$icon .= " fa-2x' aria-hidden='true'></i>";
									echo $icon;
									break;
								case -3:
									echo "<p>".$c->valore."</p>";
									break;
								case -4:
									$val = Group::where('id', $c->valore)->get();
									if(count($val)>0){
										$val2 = $val[0];
										echo $val2->nome;
									}else{
										echo "";
									}
								break;
							}
						}						
						echo "</td>";
					}
					//aggiungo celle vuote per completare la riga
					for($i=0; $i<count($input['spec'])-count($specs); $i++){
						echo "<td>n.d.</td>";
					}
                		echo "</tr>";
				}
        		}
			echo "</table>";
			echo "<p><b>Totale iscritti: $tot_iscritti</b></p>";
			$w++;
		}
	}

}

$keys = ['week_filter', 'week_filter_id', 'week_filter_value', 'spec', 'spec_filter', 'spec_filter_id', 'spec_filter_value'];
foreach($keys as $key){
	if(!array_key_exists($key, $input)){			
		$input[$key] = array();
	}
}
$whereRaw = "sub.id_event = ".Session::get('work_event');

/* if($group>0){
		$selects = TypeSelect::where('id_type', $group)->orderBy('ordine', 'asc')->get();
		foreach($selects as $select){
			echo "<h4>".$select->option."</h4>";
			stampa_tabella($specs, $spec_users, $columSpecs, $select->id, $whereRaw, $filter_id, $filter_value, $filter, $att_filter, $att_filter_id, $att_filter_value, $att_spec );
		}

}else{ */
	stampa_tabella($input);

//}

	?>
<br>

</div>
</body>
</html>

