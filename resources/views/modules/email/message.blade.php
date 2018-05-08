<?php
use Modules\Oratorio\Entities\Oratorio;
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
<h1>{{$title}}</h1>
{!! $content !!}
</body>
</html>
