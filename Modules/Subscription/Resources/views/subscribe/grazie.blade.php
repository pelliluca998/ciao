<?php
use Modules\Modulo\Entities\Modulo;

$array_moduli = json_decode($event->id_moduli);

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
          @if($array_moduli != null)

          Clicca sul pulsante qui sotto per stampare il modulo.<br>
          <div class="card-deck">
            @foreach(Modulo::whereIn('id', $array_moduli)->orderBy('label', 'ASC')->get() as $modulo)
            <div class="card">
              <div class="card-body">
                {{ $modulo->label }}
              </div>
              <div class="card-footer">
                {!! Form::open(['method' => 'GET', 'route' => ['subscription.print', $subscription->id]]) !!}
                {!! Form::hidden('id_modulo', $modulo->id) !!}
                {!! Form::submit('Stampa modulo', ['class' => 'btn btn-primary form-control']) !!}
                {!! Form::close() !!}
              </div>
            </div>

            @if(!($loop->iteration % 4))
          </div>
          <div class="card-deck">
            @endif

            @endforeach

          </div>

          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
