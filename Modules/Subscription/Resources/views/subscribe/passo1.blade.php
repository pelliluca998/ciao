<?php
use Modules\Event\Entities\Event;
use App\TypeSelect;
use Modules\Event\Entities\EventSpec;
use Modules\User\Entities\Group;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Iscrizione all'evento <i> {{$event->nome}}</i></h1>
		<p class="lead">Passo 1: inserisci le informazioni qui sotto, poi clicca su "Prosegui"</p>
		<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::open(['route' => 'subscribe.savesubscribe']) !!}

				@if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner'))
					{!! Form::hidden('type', 'ADMIN') !!}
					{!! Form::hidden('confirmed', '1') !!}
				@else
					{!! Form::hidden('type', 'WEB') !!}
					{!! Form::hidden('confirmed', '0') !!}
				@endif
				{!! Form::hidden('id_user', $id_user) !!}
				{!! Form::hidden('id_event', $event->id) !!}


				<?php
				//specifiche dell'evento
				$specs = (new EventSpec)
					->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.price')
					->where([['id_event', $event->id], ['event_specs.general', 1]])
					->orderBy('event_specs.ordine', 'ASC')
					->get();
				?>
				@foreach($specs as $spec)
					{!! Form::hidden('id_spec['.$loop->index.']', $spec->id) !!}
					<?php
						$price = json_decode($spec->price, true);
						if(count($price)==0) $price[0]=0;
					?>
					{!! Form::hidden('costo['.$loop->index.']', $price[0]) !!}
					@if(Auth::user()->hasRole('user') && $spec->hidden==1)
						{!! Form::hidden('specs['.$loop->index.']', 0) !!}
						{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
					@else
						<div class="form-group" >
							<table style="width: 100%;">
							@if(!Auth::user()->hasRole('user'))
								<tr><td style="width: 80%;"></td><td style="width: 10%;"></td></tr>
							@else
								<tr><td style="width: 100%;"></td></tr>
							@endif
							<tr><td>
							{!! Form::label($spec->id, $spec->label) !!}
							@if(strlen($spec->descrizione)>0)
								- <i>{!! Form::label($spec->descrizione, $spec->descrizione) !!}</i>
							@endif

							@if(floatval($price[0])>0)
								(Prezzo: {{number_format(floatval($price[0]), 2, ',', '')}}€)
							@endif
							@if($spec->id_type>0)
								{!! Form::select('specs['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control'])!!}
							@else
								@if($spec->id_type==-1)
									{!! Form::text('specs['.$loop->index.']', '', ['class' => 'form-control']) !!}
								@elseif($spec->id_type==-2)
									{!! Form::hidden('specs['.$loop->index.']', 0) !!}
									{!! Form::checkbox('specs['.$loop->index.']', 1, '', ['class' => 'form-control']) !!}
								@elseif($spec->id_type==-3)
									{!! Form::number('specs['.$loop->index.']', '', ['class' => 'form-control']) !!}
								@elseif($spec->id_type==-4)
									{!! Form::select('specs['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), '', ['class' => 'form-control'])!!}
								@endif
							@endif
							</td>
							@if(!Auth::user()->hasRole('user'))
								<td>

									{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
									@if(floatval($price[0])>0)
										{!! Form::label($spec->id, 'Pagato') !!}
										{!! Form::checkbox('pagato['.$loop->index.']', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
							@else
								{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
							@endif

							</tr>
							</table>
						</div>
					@endif
				@endforeach

				<div class="form-group">
					@if(count($specs)>0)
						{!! Form::submit('Prosegui!', ['class' => 'btn btn-primary form-control']) !!}
					@else
						{!! Form::submit('Questo evento non prevede delle specifiche generali, vai al passo 2!', ['class' => 'btn btn-primary form-control']) !!}
					@endif
				</div>
           		{!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
