<?php
use App\Event;
?>

@extends('layouts.app')

@section('content')
<h1>Nuovo Evento</h1>
<p class="lead">Inserisci le informazioni per il nuovo evento qui sotto, oppure <a href="{{ route('events.index') }}">torna all'elenco completo.</a></p>
<hr>
<div class="container">
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
			{!! Form::open(['route' => 'events.store']) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('anno', 'Anno') !!}
				{!! Form::number('anno', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
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
