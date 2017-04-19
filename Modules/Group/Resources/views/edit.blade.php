<?php
use App\User;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1>Modifica gruppo</h1>
	<p class="lead">Modifica e salva il gruppo qui sotto, oppure <a href="{{ route('group.index') }}">torna all'elenco completa.</a></p>
	<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-heading">Gruppo</div>
		<div class="panel-body">	
			{!! Form::model($group, ['method' => 'PATCH','route' => ['group.update', $group->id]]) !!}
				{!! Form::hidden('id_group', $group->id) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
				</div>
								
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
