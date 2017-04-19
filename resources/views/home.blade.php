<?php
use App\Week;
use App\Event;
use App\SpecSubscription;
use App\User;
use App\CampoWeek;
use App\Oratorio;
use App\LicenseType;
use App\UserOratorio;
?>

@extends('layouts.app')
@section('content')
<div class="container">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Benvenuto!</div>


                <div class="panel-body">                
				@if (Session::get('session_oratorio')!=null)					 	
					Ciao {{Auth::user()->name}}, qui sotto trovi la lista degli eventi che il tuo oratorio ha creato. Clicca sulla bandiera accanto all'evento per iscriverti e inserire ulteriori dettagli!<br><br>
				
					<?php
					$user_oratorio = UserOratorio::where('id_user', Auth::user()->id)->get();
					?>
					
		              @foreach($user_oratorio as $uo)
		              		<div style="width: 100%; float: left;">
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
		              		
						@foreach($events as $event)
							{!! Form::open(['route' => 'subscribe.create']) !!}
							{!! Form::hidden('id_event', $event->id); !!}
							{!! Form::hidden('id_user', Auth::user()->id); !!}
							@if($event->color!='')
								{{$color = $event->color}}
							@endif
							<div class="box-subscribe" style= "background: {{$color}} url('{{url(Storage::url('public/'.$event->image))}}') no-repeat; background-size: cover;')">
								<div class="box-subscribe-title">
									<b>{!!$event->nome!!}</b>
								</div>
								<div class="box-subscribe-description">
									{!!$event->descrizione!!}
								</div>
						
								<div style="margin-top: 5px;">
								{!! Form::submit('Iscriviti', ['class' => 'btn btn-primary form-control']) !!}
								{!! Form::close() !!}
								</div>
							</div>
						@endforeach
						</div>
					@endforeach
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
</div>
@endsection
