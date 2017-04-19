<?php
use App\User;
?>

@extends('layouts.app')

@section('content')
 
<div class="container">
	<div class="row">
		<h1>Nuovo Gruppo</h1>
	<p class="lead">Inserisci le informazioni per il nuovo gruppo qui sotto, oppure <a href="{{ route('group.index') }}">torna all'anagrafica completa.</a></p>
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
			{!! Form::open(['route' => 'group.store']) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
				</div>

				{!! Form::hidden('id_oratorio', Session::get('session_oratorio')) !!}				

				<div class="form-group">
				{!! Form::submit('Salva Gruppo', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
