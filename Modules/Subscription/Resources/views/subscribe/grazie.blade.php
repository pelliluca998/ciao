<?php
use App\Event;
?>

@extends('layouts.app')

@section('content')


<hr>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			GRAZIE, ISCRIZIONE SALVATA!
			<p>{!! Event::leftJoin('subscriptions', 'subscriptions.id_event', 'events.id')->where('subscriptions.id', $id_subscription)->first()->grazie !!}</p>
			Clicca sul pulsante qui sotto per stampare la ricevuta.
			{!! Form::open(['route' => 'subscription.print', 'target' => '_blank', 'method' => 'GET']) !!}
				{!! Form::hidden('id_subscription', $id_subscription) !!}
				{!! Form::submit('Stampa!', ['class' => 'btn btn-primary form-control']) !!}
			{!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
