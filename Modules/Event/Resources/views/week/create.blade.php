<?php
use App\User;
?>

@extends('layouts.app')

@section('content')
 

<div class="container">
	<div class="row">
		<h1>Nuovo Settimana</h1>
		<p class="lead">Inserisci le date per la nuova settimana, oppure <a href="{{ route('week.index') }}">torna all'elenco completo.</a></p>
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
			{!! Form::open(['route' => 'week.store']) !!}
				<div class="form-group">
				{!! Form::label('from_date', 'Data inizio') !!}
				{!! Form::text('from_date', null, ['class' => 'form-control','id' => 'datepicker']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('to_date', 'Data fine') !!}
				{!! Form::text('to_date', null, ['class' => 'form-control','id' => 'datepicker2']) !!}
				</div>		
				
				
				<div class="form-group">
				{!! Form::submit('Salva settimana', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
