<?php
use Modules\Event\Entities\Week;
use Modules\User\Entities\Group;
use Modules\Event\Entities\Event;
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
<body>


<div style="border:1; margin: 20px;">
<?php

$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));

?>
<p style="text-align: center;">{!! $oratorio->nome !!}</p>
<h3 style="text-align: center;">Report anagrafica</h3>
<?php
function stampa_tabella($input, $whereRaw){
	/*if($select_value>0){
$subs = DB::table('subscriptions as sub')->select('sub.id as id_subs', 'users.*', 'sub.type', 'sub.confirmed', 'users.id as id_user')->leftJoin('users', 'users.id', '=', 'sub.id_user')->leftJoin('event_spec_values', 'event_spec_values.id_subscription', '=', 'sub.id')->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')->whereRaw('event_spec_values.valore = '.$select_value.' AND event_specs.id_type > 2 AND '.$whereRaw)->orderBy('users.cognome', 'asc')->orderBy('users.name', 'asc');

	}else{*/
		$subs = User::select('users.*')
		->leftJoin('user_oratorio', 'user_oratorio.id_user', 'users.id')
		->whereRaw($whereRaw)->orderBy('users.cognome', 'asc')->orderBy('users.name', 'asc');
	//}
	//echo $subs->toSql();
	$subs = $subs->get();
	echo "<table class='testgrid'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";

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
	echo "</thead>";
	

	foreach($subs as $sub){
		$r=0;
		$filter_ok=true;

		$r=0;
		foreach($input['att_filter'] as $fa){
			if($fa==1 && $filter_ok){
				$at = AttributoUser::where([['id_user', $sub->id], ['id_attributo', $input['att_filter_id'][$r]], ['valore', $input['att_filter_value'][$r]]])->get();
				if(count($at)==0) $filter_ok=false;
			}
			$r++;
		}

		if($filter_ok){
		echo "<tr>";
		echo "<td>".$sub->id."</td>";

		//SPECIFICHE UTENTE
		if(count($input['spec_user'])>0){
			foreach($input['spec_user'] as $user){
				if($user=='photo' && $sub->$user!=''){

					echo "<td><img src='".url("upload/".$sub->id_oratorio."/user_profile/".$sub->photo)."' width=60px/></td>";
				}else{
					echo "<td>".$sub->$user."</td>";
				}

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
	}

$keys = ['spec_user', 'user_filter', 'user_filter_id', 'user_filter_value', 'att_filter', 'att_filter_id', 'att_filter_value', 'att_spec'];
foreach($keys as $key){
	if(!array_key_exists($key, $input)){			
		$input[$key] = array();
	}
}

$whereRaw = "user_oratorio.id_oratorio = ".Session::get('session_oratorio');
$i=0;
foreach($input['user_filter'] as $f){
	if($f=='1'){
		if($whereRaw!='') $whereRaw .= " AND ";
		$whereRaw .= " users.".$input['user_filter_id'][$i]." LIKE '%".$input['user_filter_value'][$i]."%'";
	}
	$i++;
}

if($whereRaw=='') $whereRaw=" 1 ";
stampa_tabella($input, $whereRaw);

?>
<br>

</div>
</body>
</html>

