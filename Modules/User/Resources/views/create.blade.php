<?php
use App\User;
?>

@extends('layouts.app')

@section('content')
 

<div class="container">
	<div class="row">
		<h1>Nuovo Utente</h1>
	<p class="lead">Inserisci le informazioni per il nuovo utente qui sotto, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
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
			{!! Form::open(['route' => 'user.store', 'files' => true]) !!}
				<div class="form-group">
				{!! Form::label('name', 'Nome') !!}
				{!! Form::text('name', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('cognome', 'Cognome') !!}
				{!! Form::text('cognome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('sesso', 'Sesso') !!}
				<select id="sesso" class="form-control" name="sesso"><option value="M">Uomo</option><option value="F">Donna</option></select>
				</div>
				
				<div class="form-group">
				{!! Form::label('nato_il', 'Data di Nascita (nel formato GG/MM/AAAA)') !!}
				{!! Form::text('nato_il', '', ['class' => 'form-control', 'id' => 'datepicker']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('nato_a', 'Luogo di Nascita') !!}
				{!! Form::text('nato_a', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('residente', 'Residente a') !!}
				{!! Form::text('residente', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('via', 'Indirizzo') !!}
				{!! Form::text('via', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('email', 'Email') !!}
				{!! Form::text('email', null, ['class' => 'form-control']) !!}
				</div>
			
				<div class="form-group">
				{!! Form::label('cell_number', 'Numero cellulare') !!}
				{!! Form::text('cell_number', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('password', 'Password') !!}
				{!! Form::password('password', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('username', 'Username') !!}
				{!! Form::text('username', null, ['class' => 'form-control']) !!}
				</div>
			
				<div class="form-group">
					<div class="form-group" style="width: 48%; float: left;">
						{!! Form::label('photo', 'Foto Profilo') !!}
						{!! Form::file('photo', null, ['class' => 'form-control']) !!}
					</div>

				</div>

				
				<div class="form-group">
				{!! Form::submit('Salva Utente', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
