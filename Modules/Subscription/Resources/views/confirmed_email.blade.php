<?php
use App\Oratorio;
?>

<html>
<head></head>
<body>
<div style='text-align: center;'>
	<?php
	$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));	
	?>
	@if($oratorio->logo!='')
		<img src="{{url(Storage::url('public/'.$oratorio->logo))}}" width='170px'>
	@endif
	<h2>{{$oratorio->nome}}</h2>
	<br>
</div>

<p>Ciao {{$user}}, la tua iscrizione all'evento <b>{{$event_name}}</b> è stata approvata!</p>
<p>Accedi alla tua area personale per vederne i dettagli.</p><br><br>
</p>A presto,<br>{{$oratorio->nome}}</p>
</body>
</html>
