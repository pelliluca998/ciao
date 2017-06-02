<?php
use App\Event;
?>

@extends('layouts.app')

@section('content')
<h1>Modifica Evento</h1>
<p class="lead">Modifica e salva l'evento qui sotto, oppure <a href="{{ route('users.index') }}">torna all'elenco degli eventi.</a></p>
<hr>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">	
			{!! Form::model($event, ['method' => 'PATCH','route' => ['events.update', $event->id]]) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
				<?php
				$checked=false;
				if ($event->active==1) $checked=true;
				?>
				<div class="form-group">
				{!! Form::label('active', 'Attivo') !!}
				{!! Form::checkbox('active', null, $checked, ['class' => 'form-control']) !!}
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
