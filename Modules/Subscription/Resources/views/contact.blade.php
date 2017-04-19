<?php
use App\EventSpec;
use App\Type;
use App\TypeSelect;
use App\Attributo;
?>

@extends('layouts.app')

@section('content')

<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Contatta gli iscritti</div>
		<div class="panel-body">
			Attraverso questa pagina puoi filtrare gli utenti iscritti al tuo evento a cui inviare un'email o un sms. Metti la spunta ai campi che vuoi filtrare e il contenuto del filtro, scegli come contattarli e poi clicca su "Invia".<br><br>
			<!--<div class="panel panel-default" style="width: 100%; float: left;">//-->
				<h4>Infomazioni <b>riguardanti l'iscrizione</b>:</h4>
				{!! Form::open(['route' => 'subscription.contact_send']) !!}
				<?php
				$id_event=Session::get('work_event');

				$specs = (new EventSpec)->select('event_specs.label', 'event_specs.id', 'types.label as type', 'types.id as id_type')->leftJoin('types', 'event_specs.id_type', '=', 'types.id')->where('event_specs.id_event', $id_event)->orderBy('event_specs.label', 'asc')->get();
				echo "<table class='testgrid' id=''>";
				echo "<thead><tr>";
				echo "<th style='width: 60%;'>Specifica</th>";
				echo "<th>Filtra?</th>";
				echo "<th>Valore del filtro:</th>";
				echo "</tr></thead>";
				
				$t=0;
				foreach($specs as $spec){	
					echo "<tr>";
					echo "<td>".$spec->label."</td>";
					echo "<td><input type='hidden' name='filter[".$t."]' value='0'/><input name='filter[".$t."]' value='1' type='checkbox'/></td>";

					$r = "<td><input name='filter_id[$t]' type='hidden' value='".$spec->id."'/>";
					if($spec->type=="text"){
						$r .= "<input name='filter_value[".$t."]' type='text'>";
					}else if($spec->type=="checkbox"){
						$r .= "<input name='filter_value[".$t."]' type=hidden value='0' >";
						$r .= "<input name='filter_value[".$t."]' type='checkbox' value='1'>";
					}else{
						$r .= Form::select("filter_value[$t]", TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'style' => 'width: inherit;']);
					}

					echo $r."</td></tr>";
					$t++;
				}

				//echo "</tr>";
				echo "</table><br>";

				?>

				<h4>Infomazioni <b>riguardanti gli utenti</b>:</h4>
				<?php
				echo "<table class='testgrid' id=''>";
				echo "<thead><tr>";
				echo "<th style='width: 60%;'>Specifica</th>";
				echo "<th>Filtra?</th>";
				echo "<th>Valore del filtro:</th>";
				echo "</tr></thead>";

				$c = [];
				$c[] = ['id'=>'name', 'label'=>'Nome'];
				$c[] = ['id'=>'cognome', 'label'=>'Cognome'];
				$c[] = ['id'=>'email', 'label'=>'Email'];
				$c[] = ['id'=>'cell_number', 'label'=>'Numero Cell.'];
				$c[] = ['id'=>'username', 'label'=>'Username'];
				$c[] = ['id'=>'nato_il', 'label'=>'Data di nascita'];
				$c[] = ['id'=>'nato_a', 'label'=>'Luogo di nascita'];
				$c[] = ['id'=>'sesso', 'label'=>'Sesso'];
				$c[] = ['id'=>'residente', 'label'=>'Residenza'];
				$c[] = ['id'=>'via', 'label'=>'Indirizzo'];
				//var_dump($c);
				$t=0;
				foreach($c as $column){	
					echo "<tr>";
					echo "<td>".$column['label']."</td>";
					echo "<td><input type='hidden' name='user_filter[".$t."]' value='0'/><input name='user_filter[".$t."]' value='1' type='checkbox'/></td>";
					$r = "<td><input name='user_filter_id[$t]' type='hidden' value='".$column['id']."'/>";
					$r .= "<input name='user_filter_value[".$t."]' type='text'>";
					echo $r."</td>";
					echo "</tr>";
					$t++;
				}

				echo "</tr>";
				echo "</table><br>";

				?>
				<h4>Attributi degli utenti:</h4>
				<?php
				echo "<table class='testgrid' id=''>";
				echo "<thead><tr>";
				echo "<th style='width: 60%;'>Attributo</th>";
				echo "<th>Filtra?</th>";
				echo "<th>Valore del filtro:</th>";
				echo "</tr></thead>";
				$attributos = Attributo::select('attributos.*', 'types.label as type')->leftJoin('types', 'attributos.id_type', '=', 'types.id')->where('attributos.id_oratorio', Session::get('session_oratorio'))->orderBy('ordine', 'ASC')->get();
				$t=0;
				foreach($attributos as $a){
					echo "<tr>";
					echo "<td>".$a->nome."</td>";
					echo "<td><input type='hidden' name='att_filter[".$t."]' value='0'/><input name='att_filter[".$t."]' value='1' type='checkbox'/></td>";

					$r = "<td><input name='att_filter_id[$t]' type='hidden' value='".$a->id."'/>";
					if($a->type=="text"){
						$r .= "<input name='att_filter_value[".$t."]' type='text'>";
					}else if($a->type=="checkbox"){
						$r .= "<input name='att_filter_value[".$t."]' type=hidden value='0' >";
						$r .= "<input name='att_filter_value[".$t."]' type='checkbox' value='1'>";
					}else{
						$r .= Form::select("att_filter_value[$t]", TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'style' => 'width: inherit;']);
					}

					echo $r."</td></tr>";
					$t++;
				}
				echo "</table><br>";
				?>
				
				<h4>Come vuoi contattare gli utenti?</h4>
				{!! Form::radio('type', 'sms', true) !!} SMS<br>
				{!! Form::radio('type', 'email', false) !!} Email<br>
				{!! Form::radio('type', 'telegram', false) !!} Telegram<br>
				{!! Form::submit('Invia!', ['class' => 'btn btn-primary form-control']) !!}
				{!! Form::close() !!}
			<!--</div>//-->
			
			<!--<div class="panel panel-default" style="width: 50%; float: left;">
				<div class="panel-heading">Report 2</div>
			</div>//-->
			
			
			
           		
                   
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
