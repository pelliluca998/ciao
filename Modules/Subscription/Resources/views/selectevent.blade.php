<?php
use App\User;
use App\Event;
?>

@extends('layouts.app')

@section('content')
 
<div class="container">
	<div class="row">
		<h1>Iscrivi utente all'evento:</h1>
		<p class="lead">Seleziona l'evento a cui iscrivere l'utente selezionato, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
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
			{!! Form::open(['route' => 'subscribe.create']) !!}
				{!! Form::hidden('id_user', $id_user) !!}
				<div class="form-group">
				{!! Form::label('id_event', 'Evento') !!}
				{!! Form::select('id_event', Event::where('id_oratorio', Session::get('session_oratorio'))->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::submit('Assegna', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
