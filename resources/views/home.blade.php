<?php
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use App\SpecSubscription;
use Modules\User\Entities\User;
use App\CampoWeek;
use Modules\Oratorio\Entities\Oratorio;
use App\LicenseType;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\Famiglia\Entities\ComponenteFamiglia;

$user_oratorio = UserOratorio::where('id_user', Auth::user()->id)->get();

//Controllo il profilo
$profilo_error = false;
$profilo = "<ul>";
if(Auth::user()->cod_fiscale == null || Auth::user()->cod_fiscale == ""){
  $profilo_error = true;
  $profilo .= "<li>Codice fiscale mancante</li>";
}
if(Auth::user()->tessera_sanitaria == null || Auth::user()->tessera_sanitaria == ""){
  $profilo_error = true;
  $profilo .= "<li>Tessera sanitaria mancante</li>";
}

$profilo .= "</ul>";

//controllo la Famiglia, se il modulo è abilitato
$modulo_famiglia = (Module::find('famiglia') != null && Module::find('famiglia')->enabled());
$padre = null;
$madre = null;
if($modulo_famiglia){
  $padre = ComponenteFamiglia::getPadre(Auth::user()->id);
  $madre = ComponenteFamiglia::getMadre(Auth::user()->id);
}
?>

@extends('layouts.app')
@section('content')
<div class="container">

  @if(Session::has('flash_message'))
  <div class="alert alert-success">
    {{ Session::get('flash_message') }}
  </div>
  @endif

  <div class="row justify-content-center" style="margin-bottom: 20px;">
    <div class="col-10">
      <div class="card">
        <div class="card-body">
          <div class="card-deck">
            <div class="card">
              <div class="card-header">Profilo</div>
              <div class="card-body" style="text-align: center">
                <!--  Controlla se mancano qualche informazioni sul profilo -->
                @if($profilo_error)
                <p><i class='fas fa-exclamation-circle faa-flash animated' style='color: Tomato;'></i> Alcuni dati dal tuo profilo sono mancanti!</p>
                {!! $profilo !!}
                @else
                @if(Auth::user()->photo == '')
    							@if(Auth::user()->sesso == "M")
    								<img src='{{ url("boy.png") }}' />
    							@elseif(Auth::user()->sesso == "F")
    							 <img src='{{ url("girl.png") }}' />
    							@endif

    						@else
    							<img src='{{ url(Storage::url("public/".Auth::user()->photo))}} ' width=48px />
    						@endif

                <br>{{ Auth::user()->full_name }}

                @endif
              </div>
              <div class="card-footer"><a href="{{ route('profile.show') }}" class="btn btn-sm btn-primary btn-block">Apri profilo</a></div>
            </div>

            @if($modulo_famiglia)
            <div class="card">
              <div class="card-header">Famiglia</div>
              <div class="card-body" style="text-align: center">
                <!--  Controlla se è stata definita la Famiglia -->
                @if($padre == null || $madre == null)
                <p><i class='fas fa-exclamation-circle faa-flash animated' style='color: Tomato;'></i> Sembra che tu non abbia definito la tua famiglia. Questo è un dato molto utile per il tuo oratorio!</p>
                @else
                <div class="form-row">
      						<div class="form-group col">
                    <!--  Padre -->
                    @if($padre->photo == '')
        							@if($padre->sesso == "M")
        								<img src='{{ url("boy.png") }}' />
        							@elseif($padre->sesso == "F")
        							 <img src='{{ url("girl.png") }}' />
        							@endif

        						@else
        							<img src='{{ url(Storage::url("public/".$padre->photo))}} ' width=48px />
        						@endif

                    <br>{{ $padre->full_name }}<br>Padre
                  </div>
                  <div class="form-group col">
                    <!--  Madre -->
                    @if($madre->photo == '')
        							@if($madre->sesso == "M")
        								<img src='{{ url("boy.png") }}' />
        							@elseif($madre->sesso == "F")
        							 <img src='{{ url("girl.png") }}' />
        							@endif

        						@else
        							<img src='{{ url(Storage::url("public/".$madre->photo))}} ' width=48px />
        						@endif

                    <br>{{ $madre->full_name }}<br>Madre
                  </div>
                </div>
                @endif
              </div>
              <div class="card-footer"><a href="{{ route('famiglia.user') }}" class="btn btn-sm btn-primary btn-block">Apri famiglia</a></div>
            </div>
            @endif

            <div class="card">
              <div class="card-header">Iscrizioni</div>
              <div class="card-body">
                <!--  Controlla se ci sono iscrizioni confermate -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  @foreach($user_oratorio as $uo)
  <div class="row justify-content-center">
    <div class="col-10">
      <div class="card">
        <div class="card-body">

          @if (Session::get('session_oratorio')!=null)
          Questa è la lista degli eventi che il tuo oratorio ha creato. Clicca su <b>Apri evento</b> per maggiori informazioni e per completare l'iscrizione.<br><br>


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
          @if($oratorio==-1)
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
