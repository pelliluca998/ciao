<html>
<head></head>
<body>
<div style="text-align: center;">
	<img src="{{url('logo_segresta.png')}}" width='120px' >
	<h2>Nuovo messaggio: {{$titolo}}</h2>
	<br>
</div>
<p>Ciao, il gestore di Segresta, Roberto, ha appena pubblicato un nuovo messaggio per tutti gli amministratori. Qui sotto il contenuto, puoi comunque vederlo nella tua homepage.</p>
<br><br>
{!! $messaggio !!}
</body>
</html>
