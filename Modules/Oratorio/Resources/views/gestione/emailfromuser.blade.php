<?php
?>

<html>
<head></head>
<body>
<div style='text-align: center;'>	
	<h2>Richiesta iscrizione nuovo oratorio</h2>
	<br>
</div>
<p>Ciao, hai ricevuto una richiesta per l'iscrizione di un nuovo oratorio:</p>
<ul>
	<li>Nome oratorio: {{$nome_oratorio}}</li>
	<li>Email oratorio: {{$email_oratorio}}</li>
	<li>Utente amministratore: {{Auth::user()->email}}</li>
</ul>
</body>
</html>
