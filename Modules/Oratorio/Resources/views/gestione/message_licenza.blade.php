<?php
use App\License;
?>
<html>
<head></head>
<body>
<div style="text-align: center;">
	<img src="{{url('logo_segresta.png')}}" width='120px' >
	<h2>Aggiornamento licenza account</h2>
	<br>
</div>
<p>Ciao <b>{{$oratorio->nome}}</b>, la licenza del tuo account su Segresta è stata aggiornata. Di seguito i moduli ora attivi:</p>
<table class="testgrid">
	<tr><thead><th>Modulo</th><th>Data attivazione</th><th>Data scadenza</th></thead></tr>
	@foreach(License::where('id_oratorio', $oratorio->id)->get() as $licenza)
	<tr>
		<td>{!! Module::find($licenza->module_name)->getDescription() !!}</td>
		<td>{{$licenza->data_inizio}}</td>
		<td>{{$licenza->data_fine}}</td>
	</tr>
	@endforeach
</table><br>
<br><a href="http://www.segresta.it/negozio" class="btn btn-primary">Acquista altri moduli</a>
<br><br><br>
Buon lavoro,
Roberto
</body>
</html>
