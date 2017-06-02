<?php
use App\EventSpec;
use App\Week;
use App\Group;
use App\Type;
use App\TypeSelect;
use App\Attributo;
?>

@extends('layouts.app')

@section('content')

<div class="container" style="">
	<div class="row">
		<h1>Report iscrizioni generale</h1>
		<p>Attraverso questa pagina puoi generare il report delle iscrizioni dell'evento corrente. Oltre a quelle di base, puoi scegliere quali informazioni inserire nel report. Puoi anche settare un filtro a uno o più campi, mettendo una spunta nella colonna "Filtra" e indicando il valore del filtro.</p>
		<hr>
	</div>
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Stampa report iscrizioni</div>
		<div class="panel-body">
			<div style="width: 100%; float: left;  padding: 5px;">
				<h4>Passo 1: Scegli le infomazioni <b>riguardanti l'iscrizione</b> da inserire nel report:</h4>
				{!! Form::open(['route' => 'report.gen_eventspec']) !!}
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
					<input name="filter[{{$loop->index}}]" value="1" type="checkbox" onchange="disable_select(this, 'filter_value_{{$loop->index}}', true)"/></td>
					<td>
						<input name="filter_id[{{$loop->index}}]" type="hidden" value="{{$spec->id}}"/>
					
					@if($spec->id_type>0)
						{!! Form::select('filter_value['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "filter_value_".$loop->index])!!}
					@else
						@if($spec->id_type==-1)
							{!! Form::text('filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "filter_value_".$loop->index]) !!}
						@elseif($spec->id_type==-2)
							{!! Form::hidden('filter_value['.$loop->index.']', 0) !!}
							{!! Form::checkbox('filter_value['.$loop->index.']', 1, '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "filter_value_".$loop->index]) !!}
						@elseif($spec->id_type==-3)
							{!! Form::number('filter_valore['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "filter_value_".$loop->index]) !!}
						@elseif($spec->id_type==-4)
							{!! Form::select('filter_value['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "filter_value_".$loop->index])!!}				
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

				?>
				
				@foreach($c as $column)
					<tr>
					<td><input name="spec_user[{{$loop->index}}]" value="{{$column['id']}}" type="checkbox"/></td>
					<td>{{$column['label']}}</td>
					<td><input type='hidden' name="user_filter[{{$loop->index}}]" value="0"/>
					<input name="user_filter[{{$loop->index}}]" value="1" type="checkbox" onchange="disable_select(this, 'user_filter_value_{{$loop->index}}', true)"/></td>
					<td>
						<input name="user_filter_id[{{$loop->index}}]" type="hidden" value="{{$column['id']}}"/>
					
					{!! Form::text('user_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "user_filter_value_".$loop->index]) !!}
					
					</td>
					</tr>
				@endforeach
				</table><br>
				
				
				<h4>Passo 3: Scegli gli attributi degli utenti da inserire nel report:</h4>
				
				<table class='testgrid' id=''>
				<thead><tr>
				<th>Check</th>
				<th style='width: 60%;'>Attributo</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				<?php
				$attributos = Attributo::select('attributos.*')->where('attributos.id_oratorio', Session::get('session_oratorio'))->orderBy('ordine', 'ASC')->get();
				?>
				@foreach($attributos as $a)
					<tr>
					<td><input name="att_spec[{{$loop->index}}]" value="{{$a->id}}" type="checkbox"/></td>
					<td>{{$a->nome}}</td>
					<td><input type="hidden" name="att_filter[{{$loop->index}}]" value="0"/>
					<input name="att_filter[{{$loop->index}}]" value="1" type="checkbox" onchange="disable_select(this, 'att_filter_value_{{$loop->index}}', true)"/></td>
					<td><input name="att_filter_id[{{$loop->index}}]" type="hidden" value="{{$a->id}}"/>
					@if($a->id_type>0)
						{!! Form::select('att_filter_value['.$loop->index.']', TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index])!!}
					@else
						@if($a->id_type==-1)
							{!! Form::text('att_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
						@elseif($a->id_type==-2)
							{!! Form::hidden('att_filter_value['.$loop->index.']', 0) !!}
							{!! Form::checkbox('att_filter_value['.$loop->index.']', 1, '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
						@elseif($a->id_type==-3)
							{!! Form::number('att_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
						@elseif($a->id_type==-4)
							{!! Form::select('att_filter_value['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index])!!}				
						@endif
					@endif
					</td>
					</tr>
				@endforeach
				</table><br>
				
				{!! Form::hidden('group', 0) !!}
				
				<h4>Passo 4: In quale formato vuoi il tuo report?</h4>
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
