<?php
use Modules\Event\Entities\Week;
use App\License;
use Modules\Event\Entities\Event;
use Modules\Subscription\Entities\Subscription;
use Modules\User\Entities\User;
use App\OwnerMessage;
use Modules\Sms\Http\Controllers\SmsController;
use Modules\Whatsapp\Http\Controllers\WhatsappController;

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
          @if(License::isValid('sms'))
          <!-- SmsController::printcredit() -->
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
        <div class="card-header">Licenza e moduli acquistati</div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Modulo</th>
                <th>Data attivazione</th>
                <th>Data scadenza</th>
              </tr>
            </thead>
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
    </div>

    <div class="col-6">
      <div class="card">
        <div class="card-header">Aggiornamenti di Segresta 2.0</div>
        <div class="card-body">

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
