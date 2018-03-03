<?php
use App\LicenseType;
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Nuovo Oratorio</h1>
<p class="lead">Inserisci le informazioni per il nuovo oratorio qui sotto, oppure <a href="{{ route('oratorio.showall') }}">torna all'elenco completo.</a></p>
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
			{!! Form::open(['route' => 'oratorio.store']) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('email', 'Email') !!}
				{!! Form::text('email', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_user', "Utente amministratore") !!}
				{!! Form::select('id_user', User::orderBy('email', 'ASC')->pluck('email', 'id'), null , ['class' => 'form-control'])!!}
				</div>
            
				<div class="form-group">
				{!! Form::label('license_type', "Tipo licenza") !!}
				{!! Form::select('license_type', LicenseType::orderBy('id', 'ASC')->pluck('name', 'id'), null, ['class' => 'form-control'])!!}
				</div>
			
			<div class="form-group">
				<div class="form-group" style="width: 48%; float: left; margin-right: 4%;">
					{!! Form::label('data_inizio', 'Data Inzio') !!}
					{!! Form::text('data_inizio', null, ['class' => 'form-control', 'id' => 'datepicker']) !!}
				</div>
			
				<div class="form-group" style="width: 48%; float: left;">
					{!! Form::label('data_fine', 'Data Fine') !!}
					{!! Form::text('data_fine', null, ['class' => 'form-control', 'id' => 'datepicker2']) !!}
				</div>
				
			</div>
            
                
				
				
				<div class="form-group">
				{!! Form::submit('Salva oratorio', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
