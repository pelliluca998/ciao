<?php
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpec;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1><i class="fas fa-calendar-alt" aria-hidden="true"></i> Modifica evento</h1>
		<p class="lead">Configura le caratteristiche del tuo evento</p>
		<hr>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
			<div class="panel panel-default">
				<div class="panel-body">
					@if($errors->any())
					<div class="alert alert-danger">
						@foreach($errors->all() as $error)
						<p>{{ $error }}</p>
						@endforeach
					</div>
					@endif

					{!! Form::model($event, ['method' => 'PATCH','files' => true, 'route' => ['events.update', $event->id]]) !!}
					{!! Form::hidden('id_event', $event->id) !!}
					<div class="form-group">
						<div class="form-group panel-left">
							{!! Form::label('nome', 'Nome') !!}
							{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
							{!! Form::label('anno', 'Anno') !!}
							{!! Form::number('anno', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-group">
						<div class="form-group panel-left">
							{!! Form::label('active', 'Attivo') !!}
							{!! Form::hidden('active', 0) !!}
							<p>Se attivo, l'evento sarà visibile agli utenti nella loro pagina personale e potranno iscriversi.</p>
							{!! Form::checkbox('active', 1, $event->active, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
							{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
							<p>Utile, ad esempio, se un genitore deve iscrivere più figli allo stesso evento.</p>
							{!! Form::hidden('more_subscriptions', 0) !!}
							{!! Form::checkbox('more_subscriptions', 1, $event->more_subscriptions, ['class' => 'form-control']) !!}
						</div>

					</div>



					<div class="form-group">
						{!! Form::label('descrizione', 'Descrizione') !!}
						{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group" style="min-height: 150px">
						<div class="form-group panel-left">
							{!! Form::label('stampa_anagrafica', 'Nel modulo di iscrizione, mostra anagrafica utente') !!}
							<p>Se selezionato, nella pagina delle iscrizioni e nel modulo d'iscrizion all'evento viene mostrata l'anagrafica dell'utente che ha eseguito l'iscrizione.</p>
							{!! Form::hidden('stampa_anagrafica', 0) !!}
							{!! Form::checkbox('stampa_anagrafica', 1, $event->stampa_anagrafica, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right" id="div_specs_iscrizione">
							{!! Form::label('spec_iscrizione', 'Quale specfica stampare nel modulo di iscrizione invece dell\'anagrafica?') !!}
							<p>Se non hai selezionato la casella qui a fianco, scegli quali informazioni dell'iscrizione mostrare al posto dei dati anagrafici.</p>
							<?php
							//seleziono tutte le specifiche generali dell'evento di tipo testo
							$specs = EventSpec::where([['id_event', $event->id], ['event_specs.id_type', -1], ['event_specs.general', 1]])->orderBy('event_specs.id')->get();
							$array_specifiche = json_decode($event->spec_iscrizione);
							if($array_specifiche == null) $array_specifiche = array();
							foreach($specs as $s){
								echo Form::checkbox('spec_iscrizione[]', $s->id, in_array($s->id, $array_specifiche), ['class' => '', 'id' => 'spec_iscrizione[]'])." ".$s->label."<br>";
							}
							?>
						</div>
					</div>

					<div class="form-group">
						<div class="form-group panel-left" >
							{!! Form::label('image', 'Immagine') !!}
							{!! Form::file('image', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
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

					<div class="form-group" style="min-height: 200px">
						<div class="form-group panel-left">
							{!! Form::label('color', 'Colore') !!}
							{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
						</div>

						<div class="form-group panel-right">
						</div>
					</div>



					<h4>Modulo di iscrizione</h4>
					<div class="form-group">
						<div class="form-group panel-left">
							{!! Form::label('template_file', 'Carica un template personalizzato per il modulo di iscrizione. Altrimenti verrà utilizzato quello di default.') !!}
							{!! Form::file('template_file', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
							<a href="{{ url(Storage::url('public/template/subscription_template.docx')) }}">Scarica il modulo di default.</a><br>
							{!! Form::hidden('elimina_template', 0) !!}
							@if($event->template_file == null)
							<p>Nessun modulo personalizzato caricato!</p><br>
							@else
							<a href="{{ url(Storage::url('public/'.$event->template_file.'')) }}">Scarica il modulo che hai caricato.</a><br>
							{!! Form::checkbox('elimina_template', 1, 0, []) !!} Elimina modulo caricato e usa quello di default
							@endif
						</div>
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

@endsection

@push('scripts')
<script>
$(document).ready(function(){
});
</script>
@endpush
