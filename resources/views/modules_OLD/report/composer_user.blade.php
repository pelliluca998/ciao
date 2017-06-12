<?php
use App\EventSpec;
use App\Type;
use App\TypeSelect;
use App\Attributo;
use App\Group;
?>

@extends('layouts.app')

@section('content')

<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Stampa report anagrafica</div>
		<div class="panel-body">
			Attraverso questa pagina puoi stampare il report con le informazioni dei tui utenti. Oltre a quelle di base, puoi scegliere quali informazioni inserire nel report.<br><br>



				{!! Form::open(['route' => 'report.gen_user']) !!}

				<h4>Passo 1: Scegli le infomazioni <b>riguardanti gli utenti</b> da inserire nel report:</h4>
				<table class='testgrid' id=''>
				<thead><tr>
				<th>Check</th>
				<th style='width: 60%;'>Specifica</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				
				<?php

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
				
				
				<h4>Passo 2: Scegli gli attributi degli utenti da inserire nel report:</h4>
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

				
				<h4>Passo 3: In quale formato vuoi il tuo report?</h4>
				{!! Form::radio('pdf', 'pdf', true) !!} PDF
				{!! Form::submit('Genera!', ['class' => 'btn btn-primary form-control']) !!}
				{!! Form::close() !!}
			</div>
			
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
