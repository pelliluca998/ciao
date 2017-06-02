<?php
use App\Event;
use App\EventSpec;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1>Modifica Evento</h1>
		<p class="lead">Modifica e salva l'evento qui sotto, oppure <a href="{{ route('events.index') }}">torna all'elenco degli eventi.</a></p>
		<hr>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-body">	
					{!! Form::model($event, ['method' => 'PATCH','files' => true, 'route' => ['events.update', $event->id]]) !!}
						{!! Form::hidden('id_event', $event->id) !!}
						<div class="form-group">
							{!! Form::label('nome', 'Nome') !!}
							{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>
		
						<div class="form-group">
		    					<div class="form-group" style="width: 48%; float: left;">
			   					{!! Form::label('active', 'Attivo') !!}
								{!! Form::hidden('active', 0) !!}
								{!! Form::checkbox('active', 1, $event->active, ['class' => 'form-control']) !!}
		    					</div>
		    
			    				<div class="form-group" style="width: 48%; float: left;">
				   				{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
								{!! Form::hidden('more_subscriptions', 0) !!}
								{!! Form::checkbox('more_subscriptions', 1, $event->more_subscriptions, ['class' => 'form-control']) !!}
	
			    				</div>

						</div>

						<div class="form-group">
							{!! Form::label('anno', 'Anno') !!}
							{!! Form::number('anno', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group">
							{!! Form::label('descrizione', 'Descrizione') !!}
							{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
						</div>	    

						<div class="form-group">
							<div class="form-group" style="width: 48%; float: left;">
								{!! Form::label('stampa_anagrafica', 'Nel modulo di iscrizione, mostra anagrafica utente') !!}
								{!! Form::hidden('stampa_anagrafica', 0) !!}
								{!! Form::checkbox('stampa_anagrafica', 1, $event->stampa_anagrafica, ['class' => 'form-control', 'onchange' => "disable_select(this, 'spec_iscrizione')", 'id' => 'stampa']) !!}
						    </div>

						    <div class="form-group" style="width: 48%; float: left;">
									{!! Form::label('spec_iscrizione', 'Quale specfica stampare nel modulo di iscrizione invece dell\'anagrafica?') !!}
									{!! Form::select('spec_iscrizione', EventSpec::where([['id_event', $event->id], ['event_specs.id_type', -1], ['event_specs.general', 1]])->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['class' => 'form-control']) !!}
						    </div>
						</div>

						<div class="form-group">
							<div class="form-group" style="width: 48%; float: left;">
								{!! Form::label('image', 'Immagine') !!}
								{!! Form::file('image', null, ['class' => 'form-control']) !!}
							</div>

							<div class="form-group" style="width: 48%; float: left;">
								Immagine attuale:<br>
								<?php
								if($event->image!=''){
									echo "<img src='".url(Storage::url('public/'.$event->image))."' width=200px/>";
								}else{
									echo "Nessuna immagine!<br><br>";
								}
								?>
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('color', 'Colore') !!}
							{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
						</div>
						
						<h4>Modulo di iscrizione</h4>
						
						<div class="form-group">
							{!! Form::label('firma', 'Dicitura nel campo Firma del modulo stampato dagli utenti') !!}
							{!! Form::text('firma', null, ['class' => 'form-control']) !!}
						</div>
						
						<div class="form-group">
							{!! Form::label('informativa', 'Informativa trattamento dati') !!}
							{!! Form::textarea('informativa', null, ['class' => 'form-control']) !!}
						</div>
						
						<div class="form-group">
							{!! Form::label('grazie', "Messaggio finale da mostrare all'utente prima di stampare il modulo") !!}
							{!! Form::textarea('grazie', null, ['class' => 'form-control']) !!}
						</div>


						<div class="form-group">
							 {!! Form::submit('Salva Evento', ['class' => 'btn btn-primary form-control']) !!}
						</div>
	   				{!! Form::close() !!}
   				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#stampa').change();
});
</script>
@endsection
