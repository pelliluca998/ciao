<?php

?>

@extends('layouts.app')
@section('content')


<hr>
<div class="container">
  <div class="row justify-content-center" style="margin-top: 20px;">
		<div class="col-6">
			<div class="card">
				<div class="card-body">
          <h2 style="text-align: center">Grazie, iscrizione salvata</h2>

          <p>{!! $event->grazie !!}</p>

          Clicca sul pulsante qui sotto per stampare la ricevuta.
          {!! Form::open(['method' => 'GET', 'route' => ['subscription.print', $subscription->id]]) !!}
          {!! Form::submit('Stampa!', ['class' => 'btn btn-primary form-control']) !!}
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
