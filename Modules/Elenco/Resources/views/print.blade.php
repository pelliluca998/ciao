<?php
use Modules\Event\Entities\EventSpec;
use Modules\Event\Entities\Week;
use Modules\User\Entities\Group;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Attributo\Entities\Attributo;
?>

@extends('layouts.app')

@section('content')

<div class="container" style="">
	<div class="row">
		<h1>Report elenco</h1>
		<p>Attraverso questa pagina puoi generare il report delle iscrizioni dell'evento corrente. Oltre a quelle di base, puoi scegliere quali informazioni inserire nel report. Puoi anche settare un filtro a uno o più campi, mettendo una spunta nella colonna "Filtra" e indicando il valore del filtro.</p>
		<hr>
	</div>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Stampa report iscrizioni</div>
		<div class="panel-body">
			<div style="width: 100%; float: left;  padding: 5px;">
				
				{!! Form::open(['route' => 'elenco.report']) !!}
				{!! Form::hidden('id_elenco', $elenco->id) !!}
				<h4>Passo 1: Scegli le colonne da inserire nel report:</h4>
				<?php
					$colonne = json_decode($elenco->colonne, true);
					$keys = array_keys($colonne);
				?>
				<table class='testgrid' id=''>
				<thead><tr>
				<th>Check</th>
				<th style="width: 60%;">Colonna</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				
				@foreach($colonne as $c)
					<tr>
					<input type='hidden' name='' value="{{$keys[$loop->index]}}" />
					<td><input name="colonna[{{$loop->index}}]" value="{{$keys[$loop->index]}}" type="checkbox" checked/></td>
					<td>{{$c}}</td>
					<td><input type='hidden' name="colonna_filter[{{$loop->index}}]" value="0"/>
					<input name="colonna_filter[{{$loop->index}}]" value="1" type="checkbox"/></td>
					<td>
					<input name="colonna_filter_id[{{$loop->index}}]" type="hidden" value="{{$keys[$loop->index]}}" />					
					{!! Form::hidden('colonna_filter_value['.$loop->index.']', 0) !!}
					{!! Form::checkbox('colonna_filter_value['.$loop->index.']', 1, '', ['class' => 'form-control']) !!}					
					</td>
					</tr>
				@endforeach
				</table>
				<br>
				
				<h4>Passo 2: Scegli le infomazioni <b>riguardanti l'iscrizione</b> da inserire nel report:</h4>
				<?php
				$id_event=Session::get('work_event');
				$specs = (new EventSpec)
					->select('event_specs.label', 'event_specs.id', 'event_specs.id_type as id_type')
					->where([['event_specs.id_event', $id_event], ['event_specs.general', 1]])
					->orderBy('event_specs.label', 'asc')
					->get();
				?>
				<table class='testgrid' id=''>
				<thead><tr>
				<th>Check</th>
				<th style="width: 60%;">Specifica</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				
				
				@foreach($specs as $spec)
					<tr>
					<input type='hidden' name='' value="{{$spec->id}}" />
					<td><input name="spec[{{$loop->index}}]" value="{{$spec->id}}" type="checkbox"/></td>
					<td>{{$spec->label}}</td>
					<td><input type='hidden' name="filter[{{$loop->index}}]" value="0"/>
					<input name="filter[{{$loop->index}}]" value="1" type="checkbox"/></td>
					<td>
						<input name="filter_id[{{$loop->index}}]" type="hidden" value="{{$spec->id}}" />
					
					@if($spec->id_type>0)
						{!! Form::select('filter_value['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control'])!!}
					@else
						@if($spec->id_type==-1)
							{!! Form::text('filter_value['.$loop->index.']', '', ['class' => 'form-control']) !!}
						@elseif($spec->id_type==-2)
							{!! Form::hidden('filter_value['.$loop->index.']', 0) !!}
							{!! Form::checkbox('filter_value['.$loop->index.']', 1, '', ['class' => 'form-control']) !!}
						@elseif($spec->id_type==-3)
							{!! Form::number('filter_valore['.$loop->index.']', '', ['class' => 'form-control']) !!}
						@elseif($spec->id_type==-4)
							{!! Form::select('filter_value['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), '', ['class' => 'form-control'])!!}				
						@endif
					@endif
					
					</td>
					</tr>
				@endforeach
				</table>
				<br>				
                </div>

            <div style="width: 100%; float: left;">
                <div class="panel-heading" style="">Passo 2: Scegli le infomazioni <b>anagrafiche degli utenti</b> da inserire nel report:</div>
				
				<table class='testgrid' id=''>
				<thead><tr>
				<th>Check</th>
				<th style='width: 60%;'>Specifica</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				
				<?php

				$c = [];
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
					echo "<td><input name='spec_user[$t]' value='".$column['id']."' type='checkbox'/></td>";
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
				
				<br>
				
				
				<h4>Passo 3: In quale formato vuoi il tuo report?</h4>
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
