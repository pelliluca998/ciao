<?php
use App\Event;
use App\Role;
use App\Permission;
use App\EventSpec;
use App\Type;
use App\TypeBase;
use App\Week;
?>

@extends('layouts.app')

@section('content')
<div class="container">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row">
        <div class="">
		<div class="panel panel-default">
		<div class="panel-heading">Informazioni aggiuntive eventi</div>
		<div class="panel-body">
		{!! Form::open(['route' => 'eventspecs.save']) !!}
		<table class="testgrid" id="showeventspecs">
		
		<?php			
		$specs = (new EventSpec)->where('id_event', $id_event)->get();
		$weeks = Week::where('id_event', $id_event)->orderBy('from_date', 'asc')->get();
		$index=0;
		?>
		<thead>
			<tr>
				<th>Nome</th>
				<th>Descrizione</th>
				<th>Tipo</th>
				<th>Generale</th>
				@foreach($weeks as $w)
					<th>Settimana <br>{{$w->from_date}}</th>
				@endforeach
				<th>Nascosta</th>
				<th>Del</th>
			</tr>
		</thead>
		@foreach($specs as $a)
				<?php
				$valid = json_decode($a->valid_for, true);
				$price = json_decode($a->price, true);
				$price_0 = 0; //prezzo per la colonna generale
				if(isset($price[0])){
					$price_0 = $price[0];
				}
				
				?>
			<tr id="row_{{$loop->index}}">
			{!! Form::hidden('id_spec['.$loop->index.']', $a->id) !!}
			{!! Form::hidden('event['.$loop->index.']', $id_event) !!}
			<td>
				{!! Form::text("label[".$loop->index."]", $a->label, ['class' => 'form-control', 'style' => 'width: 100%']) !!}
			</td>
			<td>
				{!! Form::text("descrizione[".$loop->index."]", $a->descrizione, ['class' => 'form-control', 'style' => 'width: 100%']) !!}
			</td>
			<td>
				{!! Form::select("id_type[".$loop->index."]", Type::getTypes(), $a->id_type, ['class' => 'form-control']) !!}
			</td>
			
			
			<td>
				{!! Form::hidden('general['.$loop->index.']', 0) !!}
				{!! Form::checkbox("general[".$loop->index."]", 1, $a->general, ['id' => "general_".$a->id, 'class' => 'form-control', "onclick" => "check_week($a->id, 0, true)"]) !!}
				<br>
				{!! Form::number("price[".$a->id."][0]", $price_0, ['class' => 'form-control', 'style' => 'width: 70px;', 'min' =>'0', 'step' => '0.01']) !!}
			</td>
			@foreach($weeks as $w)
				<td>
				
				{!! Form::hidden("valid_for[".$a->id."][".$w->id."]", 0) !!}
				@if(isset($valid[$w->id]))
					{!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, $valid[$w->id], ['id' => "check_".$a->id."_".$w->id, 'class' => 'form-control', "onclick" => "check_week($a->id, $w->id, false)"]) !!}
				@else
					{!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, 0, ['id' => "check_".$a->id."_".$w->id, 'class' => 'form-control', "onclick" => "check_week($a->id, $w->id, false)"]) !!}
				@endif
				<br>
				@php
					$price_w = 0;
					if(isset($price[$w->id])){
						$price_w = $price[$w->id];
					}
				@endphp
				{!! Form::number("price[".$a->id."][".$w->id."]", $price_w, ['class' => 'form-control', 'style' => 'width: 70px;', 'min' =>'0', 'step' => '0.01']) !!}
				</td>
			@endforeach
			
			
			
			<td>
				{!! Form::hidden('hidden['.$loop->index.']', 0) !!}
				{!! Form::checkbox("hidden[".$loop->index."]", 1, $a->hidden, ['class' => 'form-control']) !!}
			</td>
			<td>
				<button onclick="eventspec_destroy({{$a->id}}, {{$loop->index}})" style="font-size: 15px;" type='button' class="btn btn-primary btn-sm" ><i class="fa fa-trash fa-2x" aria-hidden="true"></i></button>
			</td>
			</tr>
			@php
				$index=$loop->index+1
			@endphp
		@endforeach
		
		</table><br><br>
		<input id='contatore' type='hidden' value="{{$index}}" />
		{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 45%']) !!}
		<i onclick='eventspecs_add({{$id_event}});' class='btn btn-primary' style='width: 45%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi specifica</i>
		{!! Form::close() !!}           		

                </div>
            </div>
        </div>
    </div>
</div>

<script>
	function check_week(a, w, general){
		if(general && $('#general_'+a).is(':checked')){	
				//setto tutte le settimane !check
				var weeks = $('[id^=check_'+a+']').prop('checked', false);
		}else{
			if($('#check_'+a+'_'+w).is(':checked')){
				var general = $('[id=general_'+a+']').prop('checked', false);
			}
		}
	}
</script>
@endsection
