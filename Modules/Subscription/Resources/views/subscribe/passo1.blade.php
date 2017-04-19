<?php
use App\Event;
use App\TypeSelect;
use App\EventSpec;
use App\Group;
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

				{!! Form::hidden('type', 'WEB') !!}
				{!! Form::hidden('id_user', $id_user) !!}
				{!! Form::hidden('id_event', $event->id) !!}
				{!! Form::hidden('confirmed', '0') !!}
				
				<?php
				//specifiche dell'evento
				$specs = (new EventSpec)
					->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione')
					->where([['id_event', $event->id], ['event_specs.general', 1]])
					->get();
				?>
				@foreach($specs as $spec)
					{!! Form::hidden('id_spec['.$loop->index.']', $spec->id) !!}
					@if($spec->hidden==1)
						{!! Form::hidden('specs['.$loop->index.']', 0) !!}
					@else
						<div class="form-group">
							{!! Form::label($spec->id, $spec->label) !!} - <i>{!! Form::label($spec->descrizione, $spec->descrizione) !!}</i>
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
