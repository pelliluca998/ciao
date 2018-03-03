<?php
use Modules\Event\Entities\EventSpec;
use App\Type;
?>

@extends('layouts.app')

@section('content')

<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Stampa report gruppo</div>
		<div class="panel-body">
			Attraverso questa pagina puoi stampare (o esportare in formato Excel) l'elenco dei componenti del gruppo selezionato. Oltre a quelle di base, puoi scegliere quali informazioni inserire nel report.<br><br>
			<div class="panel panel-default" style="/*width: 50%; float: left;*/">
				<div class="panel-heading">Report 1</div>
				<h4>Passo 1: Scegli le infomazioni da inserire nel report:</h4>
				{!! Form::open(['route' => 'group.report_generator']) !!}
				{!! Form::hidden('id_group', $id_group) !!}
				<?php
				$id_event=Session::get('work_event');
				echo "<table class='testgrid' id=''>";
				echo "<thead><tr>";
				echo "<th style='width: 60%;'>Specifica</th>";
				echo "<th>Check</th>";
				echo "</tr></thead>";

				$c = [];
				$c[] = ['id'=>'email', 'label'=>'Email'];
				$c[] = ['id'=>'username', 'label'=>'Username'];
				$c[] = ['id'=>'nato_il', 'label'=>'Data di nascita'];
				$c[] = ['id'=>'nato_a', 'label'=>'Luogo di nascita'];
				$c[] = ['id'=>'sesso', 'label'=>'Sesso'];
				$c[] = ['id'=>'residente', 'label'=>'Residenza'];
				$c[] = ['id'=>'via', 'label'=>'Indirizzo'];
				//var_dump($c);
				foreach($c as $column){	
					echo "<tr>";
					echo "<td>".$column['label']."</td>";
					echo "<td><input name='spec[]' value='".$column['id']."' type='checkbox'/></td>";
					echo "</tr>";
				}

				echo "</tr>";
				echo "</table><br>";

				?>

				<h4>Passo 2: In quale formato vuoi il tuo report?</h4>
				{!! Form::radio('pdf', 'pdf', true) !!} PDF
				{!! Form::submit('Genera!', ['class' => 'btn btn-primary form-control']) !!}
				{!! Form::close() !!}
			</div>






                </div>
            </div>

    </div>
</div>
       
        
<?php

?>
@endsection
	<script>
	$('link[rel=stylesheet]').remove();
</script>
