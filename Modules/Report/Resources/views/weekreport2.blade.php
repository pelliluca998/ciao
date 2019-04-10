<?php
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use Modules\User\Entities\Group;
use Modules\User\Entities\User;
use Modules\Subscription\Entities\Subscription;
use Modules\Oratorio\Entities\Oratorio;
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
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
	<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
	<title>Report</title>


</head>
<?php
function stampa_tabella($input, $whereRaw, $format){
	$event = Event::findOrFail(Session::get('work_event'));
	$weeks = Week::where('id_event', Session::get('work_event'))->orderBy('from_date', 'ASC')->get();
	$w=0;
	$array_iscrizioni = array();
	foreach($weeks as $week){
		//controllo che per la settimana corrente sia stato scelto qualche campo da stampare...
		if(isset($input['week'][$w])){
			echo "<h2>Settimana dal ".$week->from_date." al ".$week->to_date."</h2>";
			echo "<table class='table table-bordered'>";
			echo "<tr>";
			echo "<th>ID</th>";
			echo "<th>Utente</th>";
			echo "<th>Confermata</th>";
			//intestazione campi da inserire nel report
			$columnSpecs1 = (new EventSpec)
			->select('event_specs.id_type', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for')
			->whereIn('event_specs.id', $input['week'][$w])
			->get();
			foreach($input['week'][$w] as $id_spec_week){
				echo "<th>".EventSpec::find($id_spec_week)->label."</th>";
				echo "<th>Pagato?</th>";
			}

			//inserimento colonne da specifiche 1
			//$columnSpecs2 = EventSpec::whereIn('id', $input['spec'])->orderBy('id', 'asc')->get();
			foreach($input['spec'] as $id_general_spec){
				echo "<th>".EventSpec::find($id_general_spec)->label."</th>";
				echo "<th>Pagato?</th>";
			}

			if(count($input['spec_user'])>0){ //stampo l'intestazione delle colonne con specifiche utente
				foreach($input['spec_user'] as $column){
					echo "<th>".$column."</th>";
				}
			}

			if(count($input['att_spec'])>0){
				foreach($input['att_spec'] as $fa){
					$a = Attributo::findOrfail($fa);
					echo "<th>".$a->nome."</th>";
				}
			}


			echo "</tr>";
			//inserimento dati
			$subs = Subscription::select('subscriptions.id as id_sub', 'users.*', 'subscriptions.confirmed')
			->leftJoin('users', 'users.id', 'subscriptions.id_user')
			->whereRaw($whereRaw)
			->whereIn('subscriptions.id',
			EventSpecValue::select('event_spec_values.id_subscription')
			->where('event_spec_values.id_week', $week->id)
			->get()
			->toArray())
			->get();


			$tot_iscritti = 0;
			foreach($subs as $sub){
				/** Prima di stampare la riga, controllo quali campi hanno il flag Filtra==1
				** Per ogni filtro, eseguo una query e verifico se corrisponde al valore del filtro impostato
				**/
				$filter_ok=true;
				$filter_values = array_values($input['week_filter_value'][$w]);
				$f=0;
				if(isset($input['week_filter'][$w]) && count($input['week_filter'][$w])>0){
					foreach($input['week_filter'][$w] as $filter_id){
						if($filter_id>0 && $filter_ok){
							//if(isset($filter_values[$f])){
							$specs = EventSpecValue::where([['id_subscription', $sub->id_sub],['id_week', $week->id], ['id_eventspec', $filter_id], ['valore', $filter_values[$f]]])->orderBy('id_eventspec')->get();
							if(count($specs)==0) $filter_ok=false;
							//}
						}
						$f++;
					}
				}

				//filtro sulle specs1
				$f=0;
				$filter_values = array_values($input['spec_filter_value']);
				foreach($input['spec_filter'] as $filter_id){
					if($filter_id>0 && $filter_ok){
						$specs = EventSpecValue::where([['id_subscription', $sub->id_sub], ['id_eventspec', $filter_id], ['valore', $filter_values[$f]]])->orderBy('id_eventspec')->get();
						if(count($specs)==0) $filter_ok=false;
					}
					$f++;
				}

				//filtro su attributi
				$r=0;
				if(count($input['att_filter'])>0){
					foreach($input['att_filter'] as $fa){
						if($fa==1 && $filter_ok){
							$at = AttributoUser::where([['id_user', $sub->id], ['id_attributo', $input['att_spec'][$r]], ['valore', $input['att_filter_value'][$r]]])->get();
							if(count($at)==0) $filter_ok=false;
						}
						$r++;
					}
				}

				if($filter_ok){
					array_push($array_iscrizioni, $sub->id_sub);
					$tot_iscritti++;
					echo "<tr>";
					echo "<td>".$sub->id_sub."</td>";
					//controllo se stampare il nome in anagrafica o una delle specifiche indicate
					if($event->stampa_anagrafica==0){
						$array_specifiche = json_decode($event->spec_iscrizione);
						$anagrafica = EventSpecValue::where(['id_subscription' => $sub->id_sub])->whereIn('id_eventspec', $array_specifiche)->get();
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
					//get valore dei campi
					foreach($input['week'][$w] as $id_spec_week){
						$specs = EventSpecValue::select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for', 'event_spec_values.valore', 'event_spec_values.pagato')
						->leftJoin('event_specs', 'event_specs.id', 'event_spec_values.id_eventspec')
						->where([['event_spec_values.id_subscription', $sub->id_sub],['event_spec_values.id_week', $week->id], ['event_specs.id', $id_spec_week]])
						->first();

						if(count($specs)!=0){
							echo "<td>";
							if($specs->id_type>0){
								$val = TypeSelect::where('id', $specs->valore)->get();
								if(count($val)>0){
									$val2 = $val[0];
									echo $val2->option;
								}else{
									echo "";
								}
							}else{
								switch($specs->id_type){
									case -1:
									echo "<p>".$specs->valore."</p>";
									break;
									case -2:
									if($specs->valore==1){
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
									echo "<p>".$specs->valore."</p>";
									break;

								}
							}
							echo "</td>";
							echo "<td>";
							if($specs->pagato==1){
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
							echo "</td>";
						}else{
							echo "<td>n.d.</td>";
							echo "<td>n.d.</td>";
						}

					}

					//valori specs1
					foreach($input['spec'] as $id_spec){
						$specs = EventSpecValue::select('event_specs.id_type', 'event_spec_values.valore', 'event_spec_values.pagato')
						->leftJoin('event_specs', 'event_specs.id', 'event_spec_values.id_eventspec')
						->where([['event_spec_values.id_subscription', $sub->id_sub],['event_spec_values.id_eventspec', $id_spec]])
						->first();


						if($specs!=null){
							echo "<td>";
							if($specs->id_type>0){
								$val = TypeSelect::where('id', $specs->valore)->get();
								if(count($val)>0){
									$val2 = $val[0];
									echo $val2->option;
								}else{
									echo "";
								}
							}else{
								switch($specs->id_type){
									case -1:
									echo "<p>".$specs->valore."</p>";
									break;
									case -2:
									if($specs->valore==1){
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
									echo "<p>".$specs->valore."</p>";
									break;

								}
							}
							echo "</td>";
							echo "<td>";
							if($specs->pagato==1){
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
							echo "</td>";
						}else{
							echo "<td></td>";
							echo "<td></td>";
						}

					}

					//SPECIFICHE UTENTE
					if(count($input['spec_user'])>0){
						foreach($input['spec_user'] as $user){
							echo "<td>".$sub->$user."</td>";
						}
					}

					//ATTRIBUTI
					if(count($input['att_spec'])>0){
						foreach($input['att_spec'] as $at){
							$whereSpec = array('id_attributo' => $at, 'id_user' => $sub->id);

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

		}
		$w++;
	}

} ?>
<body>


	<div style="border:1; margin: 20px;">
		<?php

		$event = Event::findOrFail(Session::get('work_event'));
		$oratorio = Oratorio::findOrFail($event->id_oratorio);
		$keys = ['week_filter', 'week_filter_id', 'week_filter_value', 'spec', 'spec_filter', 'spec_filter_id', 'spec_filter_value', 'att_filter', 'att_filter_value', 'att_spec', 'spec_user'];
		foreach($keys as $key){
			if(!array_key_exists($key, $input)){
				$input[$key] = array();
			}
		}
		?>
		<p style="text-align: center;">{!! $oratorio->nome !!}</p>
		<h2 style="text-align: center;">{!! $event->nome !!}</h2>
		<h3 style="text-align: center;">Report delle iscrizioni</h3>


		<?php
		if($input['mostra_fitri'] == 1 && $input['format'] != 'excel'){
			echo "<h4>Filtri settimanali attivi: </h4>";

			$weeks = Week::where('id_event', Session::get('work_event'))->orderBy('from_date', 'ASC')->get();
			$w=0;
			echo "<ul>";
			foreach($weeks as $week){
				$filters = $input['week_filter'][$w];
				//$filter = array();
				//$filter_id = $input['week_filter_id'][$w];
				//$filter_id = array();
				//$filter_values = $input['week_filter_value'][$w];
				if(!isset($input['week_filter'][$w])){
					//potrei non aver selezionato nessun filtro per una data settimana
					//per evitare errore nel foreach, creo un array vuoto
					$input['week_filter'][$w] = array();
				}
				$valore_filtro = current($input['week_filter_value'][$w]);
				foreach($input['week_filter'][$w] as $filter){
					if($filter==0) continue;
					$spec = EventSpec::findOrFail($filter);
					echo "<li>Settimana ".($w+1)." - Specifica <b>". $spec->label."</b> con valore  ";
					if($spec->id_type>0){
						$val = TypeSelect::where('id', $valore_filtro)->get();
						if(count($val)>0){
							$val2 = $val[0];
							echo "<b>".$val2->option."</b>";
						}else{
							echo "";
						}
					}else{
						switch($spec->id_type){
							case -1:
							echo "<b>".$valore_filtro."</b>";
							break;
							case -2:
							$icon = "<i class='far ";
							if($valore_filtro==1){
								$icon .= "fa-check-circle";
							}else{
								$icon .= "fa-circle";
							}
							$icon .= " fa-2x'></i>";
							echo $icon;
							break;
							case -3:
							echo "<b>".$valore_filtro."</b>";
							break;

						}
					}
					echo "</li>";
					$valore_filtro = next($input['week_filter_value'][$w]);
				}
				$w++;
			}
			echo "</ul>";

			echo "<h4>Filtri generali attivi: </h4>";
			echo "<ul>";
			$valore_filtro = current($input['spec_filter_value']);
			foreach($input['spec_filter'] as $filter){
				if($filter==0){
					next($input['spec_filter_value']);
					continue;
				}
				$spec = EventSpec::findOrFail($filter);
				echo "<li>Specifica <b>". $spec->label."</b> con valore  ";
				if($spec->id_type>0){
					$val = TypeSelect::where('id', $valore_filtro)->get();
					if(count($val)>0){
						$val2 = $val[0];
						echo "<b>".$val2->option."</b>";
					}else{
						echo "";
					}
				}else{
					switch($spec->id_type){
						case -1:
						echo "<b>".$valore_filtro."</b>";
						break;
						case -2:
						$icon = "<i class='far ";
						if($valore_filtro==1){
							$icon .= "fa-check-circle";
						}else{
							$icon .= "fa-circle";
						}
						$icon .= " fa-2x'></i>";
						echo $icon;
						break;
						case -3:
						echo "<b>".$valore_filtro."</b>";
						break;

					}
				}
				echo "</li>";
				$valore_filtro = next($input['spec_filter_value']);

			}
			echo "</ul>";
		}

		$whereRaw = " 1 ";
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
