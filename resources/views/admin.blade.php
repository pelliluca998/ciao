<?php
use Modules\Event\Entities\Week;
use App\License;
use Modules\Event\Entities\Event;
use Modules\Subscription\Entities\Subscription;
use App\SpecSubscription;
use Modules\User\Entities\User;
use App\CampoWeek;
use App\OwnerMessage;
use Modules\Sms\Http\Controllers\SmsController;
use Modules\Whatsapp\Http\Controllers\WhatsappController;
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <h1><i class="fas fa-home" aria-hidden="true"></i> Admin Home</h1>
    <p class="lead">Bentornato nella sezione amministrativa!</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
      <div class="panel panel-default panel-left">
        <div class="panel-heading">Benvenuto! Alcune informazioni riassuntive</div>
        <div class="panel-body">

          <h3>Eventi attivi</h3>
          <table class="testgrid">
            <tr><thead><th>Evento</th><th>Iscritti</th><th>Non approvati</th></thead></tr>
            @foreach(Event::where([['active', 1], ['id_oratorio', Session::get('session_oratorio')]])->get() as $event)
            <tr>
              <td>{{$event->nome}}</td>
              <td>{{DB::table('subscriptions')->where('id_event', $event->id)->count()}}</td>
              <td>{{DB::table('subscriptions')->where([['id_event', $event->id], ['confirmed', 0]])->count()}}</td>
            </tr>
            @endforeach
          </table>

          <div class="form-group" style="min-height: 250px">

            <div class="form group panel-left">
              <h3>SMS</h3>
              @if(License::isValid('sms'))
              {!! SmsController::printcredit() !!}
              @else
              Il modulo SMS non è attivo.
              @endif
            </div>

            <div class="form group panel-right">
              <h3>WhatsApp</h3>
              @if(License::isValid('whatsapp'))
                {!! WhatsappController::printcredit() !!}<br><br>
                {!! WhatsappController::newMessage() !!}
              @else
              Il modulo WhatsApp non è attivo.
              @endif
            </div>
          </div>

          <h3>Licenza e moduli acquistati</h3>
          <table class="testgrid">
            <tr><thead><th>Modulo</th><th>Data attivazione</th><th>Data scadenza</th></thead></tr>
            @foreach(License::where('id_oratorio', Session::get('session_oratorio'))->get() as $licenza)
            <tr>
              <td>{!! Module::find($licenza->module_name)->getDescription() !!}</td>
              <td>{{$licenza->data_inizio}}</td>
              <td>{{$licenza->data_fine}}</td>
            </tr>
            @endforeach
          </table><br>
          <br><a href="http://www.segresta.it/negozio" class="btn btn-primary">Acquista altri moduli</a>


          </div>
        </div>

        <div class="panel panel-default panel-right">
          <div class="panel-heading">Aggiornamenti di Segresta 2.0</div>
          <div class="panel-body">
            @foreach(OwnerMessage::orderBy('created_at', 'DESC')->get() as $message)
            <h3>{{$message->id}} - {{$message->title}}</h3>
            {!! $message->message !!}
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
  @endsection
