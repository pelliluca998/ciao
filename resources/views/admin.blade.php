<?php
use App\Week;
use App\License;
use App\Event;
use App\Subscription;
use App\SpecSubscription;
use App\User;
use App\CampoWeek;
use App\OwnerMessage;
use Modules\Sms\Http\Controllers\SmsController;
?>

@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row">
        <div class="">
            <div class="panel panel-default panel-left">
                <div class="panel-heading">Benvenuto! Alcune informazioni riassuntive</div>
                <div class="panel-body">                	
                	<?php 
                		$licenza = License::leftJoin('license_types', 'license_types.id', 'licenses.license_type')->where('id_oratorio', Session::get('session_oratorio'))->first();
                		$sms = License::leftJoin('license_types', 'licenses.license_type', 'license_types.id')->where([['licenses.id_oratorio', Session::get('session_oratorio')], ["modules", "like", "%sms%"]])->orWhere([['licenses.data_fine', '>=', date("Y-m-d")], ['licenses.data_fine', 'null']])->get();
                	?>
                	
                	<h3>Licenza</h3>
                	<p>Licenza attiva: <b>{{$licenza->name}}</b><br>Data di attivazione: {{$licenza->data_inizio}} 
                	@if($licenza->data_fine!=null)
                		- Data di scadenza: {{$licenza->data_fine}}</p>
                	@endif
                	@if($licenza->license_type==1)
                		<br><a href="http://www.segresta.it/negozio" class="btn btn-primary">Acquista la licenza PRO!</a>
                	@endif
                	
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
				
				@if(count($sms)>0)
					<h3>SMS</h3>
					{!! SmsController::printcredit() !!}
				@endif
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
