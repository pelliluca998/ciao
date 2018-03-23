<?php
use Modules\Event\Entities\EventSpec;
use Modules\User\Entities\Group;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Attributo\Entities\Attributo;
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
				$specs = (new EventSpec)->select('event_specs.label', 'event_specs.id', 'event_specs.id_type as id_type')->leftJoin('types', 'event_specs.id_type', '=', 'types.id')->where('event_specs.id_event', $id_event)->orderBy('event_specs.label', 'asc')->get();
				?>
				<table class='testgrid' id=''>
				<thead><tr>
					<th style='width: 60%;'>Specifica</th>
					<th>Filtra?</th>
					<th>Valore del filtro:</th>
				</tr></thead>
				
				@foreach($specs as $spec)
					<tr>
										
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
				</table><br>

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
				$attributos = Attributo::select('attributos.*', 'types.label as type')->leftJoin('types', 'attributos.id_type', '=', 'types.id')->where('attributos.id_oratorio', Session::get('session_oratorio'))->orderBy('ordine', 'ASC')->get();
				?>
				<table class='testgrid' id=''>
				<thead><tr>
				<th style='width: 60%;'>Attributo</th>
				<th>Filtra?</th>
				<th>Valore del filtro:</th>
				</tr></thead>
				
				@foreach($attributos as $a)
					<tr>
					<td>{{$a->nome}}</td>
					<td><input type="hidden" name="att_filter[{{$loop->index}}]" value="0"/>
					<input name="att_filter[{{$loop->index}}]" value="1" type="checkbox"/></td>
					<td><input name="att_filter_id[{{$loop->index}}]" type="hidden" value="{{$a->id}}"/>
					@if($a->id_type>0)
						{!! Form::select('att_filter_value['.$loop->index.']', TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control'])!!}
					@else
						@if($a->id_type==-1)
							{!! Form::text('att_filter_value['.$loop->index.']', '', ['class' => 'form-control']) !!}
						@elseif($a->id_type==-2)
							{!! Form::hidden('att_filter_value['.$loop->index.']', 0) !!}
							{!! Form::checkbox('att_filter_value['.$loop->index.']', 1, '', ['class' => 'form-control']) !!}
						@elseif($a->id_type==-3)
							{!! Form::number('att_filter_value['.$loop->index.']', '', ['class' => 'form-control']) !!}
						@elseif($a->id_type==-4)
							{!! Form::select('att_filter_value['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), '', ['class' => 'form-control'])!!}				
						@endif
					@endif
					</td>
					</tr>
				@endforeach
				</table><br>
				
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
   
@endsection
	<script>
	$('link[rel=stylesheet]').remove();
</script>
