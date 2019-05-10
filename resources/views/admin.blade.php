<?php
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use Modules\Subscription\Entities\Subscription;
use Modules\User\Entities\User;
use App\OwnerMessage;
use Modules\Sms\Http\Controllers\SmsController;
use Modules\Whatsapp\Http\Controllers\WhatsappController;
use Carbon\Carbon;

$aggiornamenti=file_get_contents("https://segresta.it/aggiornamenti.php");
$aggiornamenti=json_decode($aggiornamenti, JSON_FORCE_OBJECT);
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-home'></i> Amministratore</h1>
        <p class="lead">Bentornato nella sezione amministrativa!</p>
        <hr>
      </div>
    </div>
  </div>

  @if(Session::has('flash_message'))
  <div class="alert alert-success">
    {{ Session::get('flash_message') }}
  </div>
  @endif

  <div class="row justify-content-center" style="margin-top: 20px;">

    <div class="col-4">
      <div class="card">
        <div class="card-header">Eventi attivi</div>
        <div class="card-body">

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Evento</th>
                <th>Iscritti</th>
                <th>Non approvati</th>
              </tr>
            </thead>
            @foreach(Event::where([['active', 1], ['id_oratorio', Session::get('session_oratorio')]])->get() as $event)
            <tr>
              <td>{{$event->nome}}</td>
              <td>{{DB::table('subscriptions')->where('id_event', $event->id)->count()}}</td>
              <td>{{DB::table('subscriptions')->where([['id_event', $event->id], ['confirmed', 0]])->count()}}</td>
            </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>

    <div class="col-4">
      <div class="card">
        <div class="card-header">SMS</div>
        <div class="card-body">
          @if(Module::find('sms') != null && Module::find('sms')->enabled())
          Crediti residui per l'invio di SMS: {{ SmsController::credito_attuale() }}
          <br><br>
          <a href="http://sms.elephantech.it/index.php?mofr=456880144" class="btn btn-primary" target="_blank">Ricarica</a>
          @else
          Il modulo SMS non è attivo.
          @endif
        </div>
      </div>
    </div>



  </div>

  <div class="row justify-content-center" style="margin-top: 20px;">

    <div class="col-6">
      <div class="card">
        <div class="card-header">Aggiornamenti di Segresta 2.0</div>
        <div class="card-body">
          @if(count($aggiornamenti) > 0)
          @foreach($aggiornamenti as $aggiornamento)
          <h3>{{ $aggiornamento['titolo'] }} ({{ Carbon::parse($aggiornamento['data'])->format('d/m/Y')}})</h3>
          {!! $aggiornamento['testo'] !!}
          @endforeach
          @else
          <p>Nessun aggiornamento disponibile</p>
          @endif




        </div>
      </div>
    </div>


  </div>

</div>
@endsection
