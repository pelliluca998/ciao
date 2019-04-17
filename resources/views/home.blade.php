<?php
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use App\SpecSubscription;
use Modules\User\Entities\User;
use App\CampoWeek;
use Modules\Oratorio\Entities\Oratorio;
use App\LicenseType;
use Modules\Oratorio\Entities\UserOratorio;

$user_oratorio = UserOratorio::where('id_user', Auth::user()->id)->get();
?>

@extends('layouts.app')
@section('content')
<div class="container">

  @if(Session::has('flash_message'))
  <div class="alert alert-success">
    {{ Session::get('flash_message') }}
  </div>
  @endif

  @foreach($user_oratorio as $uo)
  <div class="row justify-content-center">
    <div class="col-10">
      <div class="card">
        <div class="card-body">

          @if (Session::get('session_oratorio')!=null)
          Ciao {{Auth::user()->name}}, qui sotto trovi la lista degli eventi che il tuo oratorio ha creato. Clicca sulla bandiera accanto all'evento per iscriverti e inserire ulteriori dettagli!<br><br>


          @if(count($user_oratorio)>1)
          <h2 style="text-align: center;">{{Oratorio::findOrFail($uo->id_oratorio)->nome}}</h2>
          @endif

          <?php
          $events = (new Event)->where([['id_oratorio', $uo->id_oratorio],['active', true]])->get();
          if(count($events)==0){
            echo "<i>Nessun evento creato!</i>";
          }
          $color = "#ADD8E6";
          ?>

          <div class="card-deck">

            @foreach($events as $event)

            <div class="card">

              <div class="card-img-top" style="height: 300px; background-color: {{ ($event->color == '' || $event->color == null)?$color:$event->color }}">
                @if($event->image == '' || $event->image == null)
                <h2 class="card-title" style="text-align: center; padding-top: 30%">{{ $event->nome }}</h2>
                @else
                <img src="{!! url(Storage::url('public/'.$event->image)) !!}" style="height: 100%; width: 100%; object-fit: cover; " alt="">
                @endif
              </div>




              <div class="card-body">
                <h5 class="card-title" style="text-align: center">{{ $event->nome }}</h5>
                <p class="card-text">{!! (strlen(strip_tags($event->descrizione)) > 500) ? substr(strip_tags($event->descrizione), 0, 500) . '...' : strip_tags($event->descrizione) !!}</p>
              </div>
              <div class="card-footer">
                {!! Form::open(['method' => 'GET', 'route' => ['events.show', $event->id]]) !!}
                {!! Form::submit('Apri evento', ['class' => 'btn btn-primary form-control']) !!}
                {!! Form::close() !!}
              </div>

            </div>

            @endforeach

          </div>
          @else
          @if ($oratorio==-1)
          <p>Ciao, sembra che tu non sia associato a nessun oratorio. Se in fase di registrazione hai scelto "Nuovo oratorio", allora comunicami i dati per la nuova attivazione, altrimenti segui la procedura per affiliarti ad un oratorio</p>
          <div style="width: 49%; margin-right: 2%; float: left;">
            {!! Form::open(['route' => 'oratorio.neworatorio']) !!}
            {!! Form::submit('Nuovo oratorio!', ['class' => 'btn btn-primary form-control']) !!}
            {!! Form::close() !!}
          </div>
          <div style="width: 49%; float: left;">
            {!! Form::open(['route' => 'oratorio.affiliazione', 'method' => 'GET']) !!}
            {!! Form::submit('Nuova afffiliazione!', ['class' => 'btn btn-primary form-control']) !!}
            {!! Form::close() !!}
          </div>
          @else
          {!! Form::open(['route' => 'home.selectoratorio']) !!}
          Prima di proseguire, devi scegliere uno degli oratori a cui sei iscritto:<br>
          {!! Form::select("id_oratorio", Oratorio::whereIn('id',$oratorio)->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}<br>
          {!! Form::submit('Prosegui!', ['class' => 'btn btn-primary form-control']) !!}
          {!! Form::close() !!}
          @endif
          @endif
        </div>


      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
