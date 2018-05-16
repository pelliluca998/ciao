<?php
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpec;
use Modules\Event\Entities\Week;
use Modules\User\Entities\Group;
use Modules\Oratorio\Entities\TypeSelect;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Iscrizione all'evento</h1>
		<p class="lead">Passo 2: inserisci le altre informazioni settimanali qui sotto, poi clicca su "Salva e Concludi".</p>
		<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::open(['route' => 'subscribe.savespec']) !!}
				{!! Form::hidden('id_subscription', $id_subscription) !!}
				{!! Form::hidden('id_event', $id_event) !!}
				<?php
				$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $id_event)->orderBy('from_date', 'asc')->get();
				$index=0;
				?>
				@if(count($weeks)>0)

				@foreach($weeks as $w)
					<?php
						$specs = (new EventSpec)
						->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for', 'event_specs.price')
						->where([['id_event', $id_event], ['event_specs.general', 0]])
						->orderBy('event_specs.ordine', 'ASC')
						->get();
					?>
					@if(count($specs)>0)
					<p><b>Settimana {{$loop->index+1}} - dal {{$w->from_date}} al {{$w->to_date}}</b></p>
					<table class='testgrid' id="weektable_{{$w->id}}">
					<thead>
						<tr>
							<th style='width: 70%;'>Specifica</th>
							<th>Prezzo (€)</th>
							@if(!Auth::user()->hasRole('user')) <th>Pagato</th> @endif
							@if(!Auth::user()->hasRole('user')) <th>Acconto</th> @endif
					</tr></thead>

					@foreach($specs as $spec)
						<?php
						$valid = json_decode($spec->valid_for, true);
						?>
						@if($valid[$w->id]==1)
							{!! Form::hidden('id_eventspec['.$index.']', $spec->id) !!}
							{!! Form::hidden('id_week['.$index.']', $w->id) !!}
								<tr style="{!! (($spec->hidden && Auth::user()->hasRole('user'))?'display:none':'display:') !!}">
									<td>
										{!! Form::label($spec->id, $spec->label) !!}
										@if(strlen($spec->descrizione)>0)
											- <i>{!! Form::label($spec->descrizione, $spec->descrizione) !!}</i>
										@endif

									@if($spec->id_type>0)
										{!! Form::select('valore['.$index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $spec->valore, ['class' => 'form-control'])!!}
									@else
										@if($spec->id_type==-1)
											{!! Form::text('valore['.$index.']', $spec->valore, ['class' => 'form-control']) !!}
										@elseif($spec->id_type==-2)
											{!! Form::hidden('valore['.$index.']', 0) !!}
											{!! Form::checkbox('valore['.$index.']', 1, $spec->valore, ['class' => 'form-control']) !!}
										@elseif($spec->id_type==-3)
											{!! Form::number('valore['.$index.']', $spec->valore, ['class' => 'form-control']) !!}
										@endif
									@endif
									</td>
									<td>
										<?php
											$price = json_decode($spec->price, true);
											$acconto = json_decode($spec->acconto, true);
											if(count($price)==0) $price[$w->id]=0;
											if(count($acconto)==0) $acconto[$w->id]=0;
										?>
										@if(Auth::user()->hasRole('user'))
											{!! Form::hidden('costo_2['.$index.']', $price[$w->id]) !!}
											{!! Form::hidden('acconto_2['.$index.']', $acconto[$w->id]) !!}
											{!! Form::hidden('pagato_2['.$index.']', 0) !!}
											{{number_format(floatval($price[$w->id]), 2, ',', '')}}€
										@else
											{!! Form::number('costo_2['.$index.']', $price[$w->id], ['class' => 'form-control', 'step' => '0.1']) !!}
										@endif
									</td>

									@if(!Auth::user()->hasRole('user'))
									<td>
										{!! Form::hidden('pagato_2['.$index.']', 0) !!}
										{!! Form::checkbox('pagato_2['.$index.']', 1, false, ['class' => 'form-control']) !!}
									</td>
									<td>
										{!! Form::number('acconto_2['.$index.']', $acconto[$w->id], ['class' => 'form-control', 'step' => '0.1']) !!}
									</td>
									@endif
								</tr>


						@php
						$index++
						@endphp
						@endif

					@endforeach
					</table><br>
					@endif
				@endforeach
				@else
					<i>Nessuna settimana inserita!</i>
				@endif
			<div class="form-group">
				{!! Form::submit('Salva e Concludi!', ['class' => 'btn btn-primary form-control']) !!}
			</div>
           		{!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
