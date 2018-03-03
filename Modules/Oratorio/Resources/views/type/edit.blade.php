<?php
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Modifica Elenco</h1>
		<p class="lead">Modifica e salva le proprietà dell'elenco qui sotto, oppure <a href="{{ route('type.index') }}">torna all'elenco completo.</a></p>
		<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-heading">Elenco</div>
		<div class="panel-body">	
			{!! Form::model($type, ['method' => 'PATCH','route' => ['type.update', $type->id]]) !!}
				{!! Form::hidden('id_type', $type->id) !!}
				<div class="form-group">
				{!! Form::label('label', 'Etichetta') !!}
				{!! Form::text('label', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('description', 'Descrizione') !!}
				{!! Form::textarea('description', null, ['class' => 'form-control']) !!}
				</div>
								
				<div class="form-group">
				{!! Form::submit('Salva Elenco', ['class' => 'btn btn-primary form-control']) !!}
				</div>
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
