<?php
use Modules\Elenco\Entities\Elenco;
use Modules\Event\Entities\EventSpec;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Riempi elenco</h1>
<p class="lead">Scegli in base a quale specifica (e il suo valore) riempire l'elenco, oppure seleziona "Nessuna specifca" per riempirlo con tutti gli iscritti all'evento.</p>
<hr>
</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::open(['route' => 'elenco.riempi']) !!}
				{!! Form::hidden('id_elenco', $elenco->id) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Specifica') !!}
				{!! Form::select('spec_iscrizione', EventSpec::where('id_event', Session::get('work_event'))->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['class' => 'form-control', 'onchange' => 'change_type(this)', 'id' => 'select_spec_iscrizione']) !!}
				</div>
				
				<div class="form-group">
					{!! Form::label('nome', 'Valore della specifica') !!}
					<span id="span_type"></span>
				</div>
				
				<div class="form-group">
				{!! Form::hidden('nessuna', 0) !!}
				{!! Form::checkbox('nessuna', 1, '', ['class' => '']) !!} Riempi l'elenco con tutti gli iscritti, senza selezionare una specifica
				</div>			
				
				
				<div class="form-group">
				{!! Form::submit('Riempi!', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$('#select_spec_iscrizione').change();
});
</script>
@endsection
