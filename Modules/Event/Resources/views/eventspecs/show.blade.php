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
			<tr>
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
				{!! Form::checkbox("general[".$loop->index."]", 1, $a->general, ['class' => 'form-control']) !!}
			</td>
			@foreach($weeks as $w)
				<td>
				<?php
				$valid = json_decode($a->valid_for, true);
				?>
				{!! Form::hidden("valid_for[".$a->id."][".$w->id."]", 0) !!}
				@if(isset($valid[$w->id]))
					{!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, $valid[$w->id], ['class' => 'form-control']) !!}
				@else
					{!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, 0, ['class' => 'form-control']) !!}
				@endif
				</td>
			@endforeach
			
			
			
			<td>
				{!! Form::hidden('hidden['.$loop->index.']', 0) !!}
				{!! Form::checkbox("hidden[".$loop->index."]", 1, $a->hidden, ['class' => 'form-control']) !!}
			</td>
			<td>
				<a href="{{url('admin/eventspecs', [$a->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a>
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

<?php

?>
@endsection
