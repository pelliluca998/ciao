<?php
use App\User;
use App\Group;
?>

@extends('layouts.app')

@section('content')
 
<div class="container">
	<div class="row">
		<h1>Aggiungi utenti al gruppo:</h1>
		<p class="lead">Seleziona il gruppo a cui assegnare gli utenti selezionati, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
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
			{!! Form::open(['route' => 'groupuser.store']) !!}
				<div class="form-group">
				{!! Form::label('id_gruppo', 'Gruppo') !!}
				{!! Form::select('id_gruppo', Group::where('id_oratorio', Session::get('session_oratorio'))->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}
				</div>						
				
				{!! Form::hidden('check_user', $check_user) !!}
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
