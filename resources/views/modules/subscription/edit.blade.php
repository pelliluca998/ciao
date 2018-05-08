<?php
use Modules\Event\Entities\Event;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Modifica Iscrizione</h1>
		<p class="lead">Modifica e salva l'iscrizione qui sotto, oppure <a href="{{ route('subscription.index') }}">torna all'elenco delle iscrizioni.</a></p>
		<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::model($subscription, ['method' => 'PUT','route' => ['subscription.update', $subscription->id]]) !!}
				{!! Form::hidden('id_sub', $subscription->id) !!}
				
				<div class="form-group">
				{!! Form::label('type', 'Tipo di iscrizione') !!}
				{!! Form::select('type', array('ADMIN' => 'Admin', 'WEB' => 'Web'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				<?php
				$checked=false;
				if ($subscription->confirmed==1) $checked=true;
				?>
				{!! Form::label('confirmed', 'Confermata?') !!}
				{!! Form::hidden('confirmed', 0) !!}
				{!! Form::checkbox('confirmed', 1, $subscription->confirmed, ['class' => 'form-control']) !!}
				</div>				
				
				
				<div class="form-group">
				{!! Form::submit('Salva Iscrizione', ['class' => 'btn btn-primary form-control']) !!}
				</div>
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
