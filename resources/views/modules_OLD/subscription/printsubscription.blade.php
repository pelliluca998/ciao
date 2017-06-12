<?php
use App\Week;
use App\Event;
use App\SpecSubscription;
use App\User;
use App\Group;
use App\CampoWeek;
use App\Subscription;
use App\Oratorio;
use App\Classe;
use App\EventSpecValue;
use App\TypeSelect;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
<link href="{{ asset('/css/segresta-style.css') }}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<title>Ricevuta Iscrizione</title>

<style>
body{

}
table.testgrid { 
	border-collapse: collapse; 
	border: 1px solid #CCB;
	width: 100%;
	font-size: 14px;
}

table.testgrid tr {
	border-bottom: 1px solid #DDD;
}


table.testgrid th {
	background: #E5E5E5;
	border: 1px solid #D5D5D5;
	color: #555;
	text-align: left;
	padding-left: 5px;
	padding-right: 0px;
	padding-top: 5px;
	padding-bottom: 5px;
	white-space: nowrap; 
}

table.testgrid td {
	padding: 5px;
	border: 1px solid #E0E0E0;
}
table.testgrid i {
  font-size: 1.5em;
  cursor: pointer;
}
</style>
</head>
<body>


<div style="border:1; margin: 20px;">
<?php
$sub = Subscription::findOrFail($id_subscription);
$event = Event::findOrFail($sub->id_event);
$oratorio = Oratorio::findOrFail($event->id_oratorio);
$user = User::findOrFail($sub->id_user);
?>
<p style="text-align: center;">{!! $oratorio->nome !!}</p>
<h2 style="text-align: center;">{!! $event->nome !!}</h2>
<h3 style="text-align: center;">Modulo di iscrizione</h3>
<div style="background-color: #D0D0D0; width: 100px; position: absolute; right: 0px; top: 0px;">
Iscrizione #{!! $id_subscription !!}
</div>
@if ($event->stampa_anagrafica==1)
<h4>Dati anagrafici</h4>
<table class="testgrid">
<tr><td style="background-color: #D0D0D0;">Cognome</td><td>{!! $user->cognome !!}</td><td style="background-color: #D0D0D0;">Nome</td><td>{!! $user->name !!}</td></tr>
<tr><td style="background-color: #D0D0D0;">Residente a</td><td>{!! $user->residente !!}</td><td style="background-color: #D0D0D0;">Via</td><td>{!! $user->via !!}</td></tr>
<tr><td style="background-color: #D0D0D0;">Luogo e data di nascita</td><td>{!! $user->nato_a !!}, {!! $user->nato_il !!}</td><td></td><td></td></tr>
</table><br>
@else
    <h4>Nominativo</h4>
    <?php
    $spec = EventSpecValue::where('id_eventspec', $event->spec_iscrizione)->where('id_subscription', $id_subscription)->get();
    if(count($spec)>0){
        echo $spec[0]->valore;
    }
    ?>

@endif

<h4>Specifiche iscrizioni - Generale</h4>
<?php
$id_event=Session::get('work_event');
$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.pagato')
	->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
	->where([['event_spec_values.id_subscription', $id_subscription], ['event_specs.general', 1]])
	->orderBy('event_specs.ordine', 'asc')->get();
$importo_totale = 0.00;
$importo_pagato = 0.00;
?>

<table class='testgrid' id='showeventspecvalue'>
<thead><tr>
<th style='width: 60%;'>Specifica</th>
<th>Valore</th>
<th>Costo (€)</th>
<th>Pagato</th>
</tr></thead>

@foreach($specs as $spec)
	<tr>
	<td>{{$spec->label}}</td>
	<td>
		@if($spec->id_type>0)
			<?php $val = TypeSelect::where('id', $spec->valore)->get(); ?>
			@if(count($val)>0)
				{{$val[0]->option}}
			@endif
		@else
			@if($spec->id_type==-1)
				{{$spec->valore}}
			@elseif($spec->id_type==-2)
				@if($spec->valore==1)
					<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
				@else
					<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
				@endif
			@elseif($spec->id_type==-3)
				{{$spec->valore}}
			@elseif($spec->id_type==-4)
				<?php
					$group = Group::where('id', $spec->valore)->get();
				?>
				@if(count($group)>0)
					{{$group[0]->nome}}
				@else
					<i style="font-size: 12px;">Nessun gruppo!</i>
				@endif
				
				
			@endif
		@endif
	</td>
	<td>
		@if($spec->costo!=0)
			{{$spec->costo}} €
		@endif
		@if($spec->id_type==-2)
			@if($spec->valore==1)
				@php
					$importo_totale += floatval($spec->costo)
				@endphp
				@if($spec->pagato==1)
					@php
						$importo_pagato += floatval($spec->costo)
					@endphp
				@endif
			@endif
		@else
			@php
				$importo_totale += floatval($spec->costo)
			@endphp
			@if($spec->pagato==1)
				@php
					$importo_pagato += floatval($spec->costo)
				@endphp
			@endif
		@endif
	</td>
	<td>
		@if($spec->pagato==1)
			<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
		@else
			<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
		@endif
	</td>
	</tr>
@endforeach
</table><br>


<?php
$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $sub->id_event)->orderBy('from_date', 'asc')->get();
?>
@if(count($weeks)>0)
	<h4>Specifiche iscrizioni - Settimanali</h4>
	@foreach($weeks as $w)
		<?php
			$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_spec_values.valore', 'event_spec_values.id', 'event_specs.id_type', 'event_spec_values.costo', 'event_spec_values.pagato', 'event_specs.valid_for')
				->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
				->where([['event_spec_values.id_subscription', $id_subscription], ['event_specs.general', 0], ['event_spec_values.id_week', $w->id]])
				->orderBy('event_specs.ordine', 'asc')->get();
		?>
		@if(count($specs)>0)
		<p><b>Settimana {{$loop->index+1}} - dal {{$w->from_date}} al {{$w->to_date}}</b></p>
		<table class='testgrid' id="weektable_{{$w->id}}">
		<thead><tr>
		<th style='width: 35%;'>Specifica</th>
		<th>Valore</th>
		<th>Costo (€)</th>
		<th>Pagato</th>
		</tr></thead>

		@foreach($specs as $spec)
			<?php
			$valid = json_decode($spec->valid_for, true);
			?>
			@if($valid[$w->id]==1)		
				<tr>
					<td>{{$spec->label}}</td>
					<td>
					@if($spec->id_type>0)
						{{TypeSelect::where('id', $spec->valore)->first()->option}}
					@else
						@if($spec->id_type==-1)
							{{$spec->valore}}
						@elseif($spec->id_type==-2)
							@if($spec->valore==1)
								<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
							@else
								<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
							@endif
						@elseif($spec->id_type==-3)
							{{$spec->valore}}
						@elseif($spec->id_type==-4)
							{{Group::where('id', $spec->valore)->first()->nome}}			
						@endif
					@endif
					</td>
					<td>
						@if($spec->costo!=0)
							{{$spec->costo}} €
						@endif
						@if($spec->id_type==-2)
							@if($spec->valore==1)
								@php
									$importo_totale += floatval($spec->costo)
								@endphp
								@if($spec->pagato==1)
									@php
										$importo_pagato += floatval($spec->costo)
									@endphp
								@endif
							@endif
						@else
							@php
								$importo_totale += floatval($spec->costo)
							@endphp
								@if($spec->pagato==1)
									@php
										$importo_pagato += floatval($spec->costo)
									@endphp
								@endif
						@endif
					</td>
					<td>
						@if($spec->pagato==1)
							<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
						@else
							<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
						@endif
					</td>
					
				</tr>	
			
			@endif
			
		@endforeach
		</table><br>
		@endif
	@endforeach
@endif


<br>
@if($importo_totale!=0)
	<p style="text-align: right"><b>Importo totale: {{number_format(floatval($importo_totale), 2, ',', '')}}€</b></p>
	<p style="text-align: right"><b>Importo pagato: {{number_format(floatval($importo_pagato), 2, ',', '')}}€</b></p>
@endif


<br>
<div style="float:left; text-align: justify; text-justify: inter-word;">
<p style="font-size: 10px;">
{!! $event->informativa !!}
</p>
</div><br><bR>
<div style="width: 50%; height: 100px; border-style: solid; float: left;padding-top: 20px; padding-bottom: 20px; margin-top: 5px;">
{{ $event->firma }}
</div>
</div>
</body>
</html>

