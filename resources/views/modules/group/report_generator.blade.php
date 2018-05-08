<?php
use Modules\Event\Entities\Week;
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
use Modules\User\Entities\Group;
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
$group = Group::findOrFail($input['id_group']);
if(!array_key_exists('spec', $input)){			
	$input['spec'] = array();
}
?>
<h3 style="text-align: center;">Gruppi e utenti</h3>
<h4 style="text-align: center;">Gruppo: {!! $group->nome !!}</h4>
<?php

function stampa_tabella($input){
	$specs = $input['spec'];
	$users = User::select('users.*')->leftJoin('group_users', 'group_users.id_user', 'users.id')->where('group_users.id_group', $input['id_group'])->orderBy('users.cognome', 'asc')->orderBy('users.name', 'asc')->get();
	$columns = Schema::getColumnListing('users');
	echo "<table class='testgrid'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Cognome</th>";
	echo "<th>Nome</th>";
	
	if(count($specs)>0){
		foreach($specs as $spec){
			echo "<th>$spec</th>";
		}
	}

	echo "</tr>";
	echo "</thead>";

	foreach($users as $user){
		echo "<tr>";
		echo "<td>".$user->id."</td>";
		echo "<td>".$user->cognome."</td>";
		echo "<td>".$user->name."</td>";
		if(count($specs)>0){
			foreach($specs as $spec){
				echo "<td>".$user->$spec."</td>";
			}
		}
		echo "</tr>";
	}


	echo "</table>";
	}

stampa_tabella($input);


	?>
<br>

</div>
</body>
</html>

