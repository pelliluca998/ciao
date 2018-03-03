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
use App\TypeSelect;
use Modules\Attributo\Entities\AttributoUser;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
<link href="{{ asset('/css/segresta-style.css') }}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<title>Scheda Utente</title>


</head>
<body>


<div style="border:1; margin: 20px;">
<?php
$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
$user = User::findOrFail($id_user);
$attributos = AttributoUser::select('attributos.id_type as id_type', 'types.label as type', 'attributos.nome as label', 'attributo_users.valore')
	->leftJoin('attributos', 'attributos.id', '=', 'attributo_users.id_attributo')
	->leftJoin('types', 'types.id', '=', 'attributos.id_type')
	->where('id_user', $id_user)->orderBy('attributos.ordine', 'ASC')->get();
?>
<p style="text-align: center;">{!! $oratorio->nome !!}</p>
<h3 style="text-align: center;">Scheda Utente</h3>

<h4>Dati anagrafici</h4>
	<div style="width: 30%; float: left;">
	<?php
		if($user->photo==''){
			if($user->sesso=="M"){
				echo "<img src='".url("boy.png")."'>";
			}else if($user->sesso=="F"){
				echo "<img src='".url("girl.png")."'>";
			}

		}else{
			echo "<img src='".url(Storage::url('public/'.$user->photo))."' width=100%/>";
		}

	?>
	</div>
	<div style="width: 65%; float: left; margin-left: 10px;">
<table class="testgrid">
<tr><td style="background-color: #D0D0D0;">Cognome</td><td>{!! $user->cognome !!}</td><td style="background-color: #D0D0D0;">Nome</td><td>{!! $user->name !!}</td></tr>
<tr><td style="background-color: #D0D0D0;">Residente a</td><td>{!! $user->residente !!}</td><td style="background-color: #D0D0D0;">Via</td><td>{!! $user->via !!}</td></tr>
<tr><td style="background-color: #D0D0D0;">Luogo e data di nascita</td><td>{!! $user->nato_a !!}, {!! $user->nato_il !!}</td><td></td><td></td></tr>
</table>
	</div>
<div style="width: 100%; float: left;">
	<h4>Attributi</h4>
	<?php
	echo "<table class='testgrid' id=''>";
	echo "<thead><tr>";
	echo "<th style='width: 60%;'>Attributo</th>";
	echo "<th>Valore</th>";
	echo "</tr></thead>";

	foreach($attributos as $att){
		echo "<tr>";
		echo "<td>".$att->label."</td>";
		echo "<td>";
		if($att->id_type>0){
			$val = TypeSelect::where('id', $att->valore)->get();
			if(count($val)>0){
				$val2 = $val[0];
				echo $val2->option;
			}else{
				echo "";
			}
		}else{
			switch($att->id_type){
				case -1:
					echo "<p>".$att->valore."</p>";
					break;
				case -2:
					$icon = "<i class='fa ";
					if($att->valore==1){
						$icon .= "fa-check-square-o";
					}else{
						$icon .= "fa-square-o";
					}
					$icon .= " fa-2x' aria-hidden='true'></i>";
					echo $icon;
					break;
				case -3:
					echo "<p>".$att->valore."</p>";
					break;
				case -4:
					$val = Group::where('id', $att->valore)->get();
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
		echo "</tr>";

	}

	echo "</tr>";
	echo "</table><br>";
	?>
</div>



<br>
<br>
</div>
</body>
</html>
