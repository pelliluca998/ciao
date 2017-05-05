<?php
use App\Event;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Nuovo Evento</h1>
<p class="lead">Inserisci le informazioni per il nuovo evento qui sotto, oppure <a href="{{ route('events.index') }}">torna all'elenco completo.</a></p>
<hr>
</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<p>{{ $error }}</p>
				@endforeach
			</div>
			@endif	
			{!! Form::open(['route' => 'events.store', 'files' => true]) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('anno', 'Anno') !!}
				{!! Form::number('anno', null, ['class' => 'form-control']) !!}
				</div>
            
                <div class="form-group">
				{!! Form::label('active', 'Attivo') !!}
				{!! Form::hidden('active', 0) !!}
				{!! Form::checkbox('active', 1, 0, ['class' => 'form-control']) !!}
				</div>
            
                <div class="form-group">
				{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
				{!! Form::hidden('more_subscriptions', 0) !!}
				{!! Form::checkbox('more_subscriptions', 1,0, ['class' => 'form-control']) !!}
		</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
						{!! Form::label('image', 'Immagine') !!}
						{!! Form::file('image', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
					{!! Form::label('color', 'Colore') !!}
					{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
				</div>
				
				<h4>Modulo di iscrizione</h4>
				<div class="form-group">
				{!! Form::label('firma', 'Dicitura nel campo Firma del modulo stampato dagli utenti') !!}
				{!! Form::text('firma', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
					{!! Form::label('informativa', 'Informativa trattamento dati') !!}
					{!! Form::textarea('informativa', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
					{!! Form::label('grazie', "Messaggio finale da mostrare all'utente prima di stampare il modulo") !!}
					{!! Form::textarea('grazie', null, ['class' => 'form-control']) !!}
				</div>	
				
				
				<div class="form-group">
				{!! Form::submit('Salva Evento', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
