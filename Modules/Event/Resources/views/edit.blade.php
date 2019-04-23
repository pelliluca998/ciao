<?php
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpec;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-calendar-alt'></i> Eventi</h1>
        <p class="lead">Modifica l'evento <i>{{ $event->nome }}</i></p>
        <hr>
      </div>
    </div>
  </div>

	<div class="row justify-content-center">
		<div class="col-10">
      <div class="card">
        <div class="card-body">

					@if($errors->any())
					<div class="alert alert-danger">
						@foreach($errors->all() as $error)
						<p>{{ $error }}</p>
						@endforeach
					</div>
					@endif

					{!! Form::model($event, ['method' => 'PATCH','files' => true, 'route' => ['events.update', $event->id]]) !!}
					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('nome', 'Nome') !!}
							{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('anno', 'Anno') !!}
							{!! Form::number('anno', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('active', 'Attivo') !!}
							{!! Form::hidden('active', 0) !!}
							<p>Se attivo, l'evento sarà visibile agli utenti nella loro pagina personale e potranno iscriversi.</p>
							{!! Form::checkbox('active', 1, $event->active, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
							<p>Utile, ad esempio, se un genitore deve iscrivere più figli allo stesso evento.</p>
							{!! Form::hidden('more_subscriptions', 0) !!}
							{!! Form::checkbox('more_subscriptions', 1, $event->more_subscriptions, ['class' => 'form-control']) !!}
						</div>

						{!! Form::hidden('select_famiglia', 0) !!}
						@if(Module::find('famiglia') != null && Module::find('famiglia')->enabled())
						<div class="form-group col">
							{!! Form::label('select_famiglia', 'Richiedi l\'iscrizione per un membro della famiglia') !!}
							{!! Form::hidden('select_famiglia', 0) !!}
							<p>Se selezionato, in fase d'iscrizione verrà richiesto di selezionare un membro della famiglia dell'utente</p>
							{!! Form::checkbox('select_famiglia', 1, $event->select_famiglia, ['class' => 'form-control']) !!}
						</div>
						@endif


					</div>



					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('descrizione', 'Descrizione') !!}
							{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row" style="min-height: 150px">
						<div class="form-group col">
							{!! Form::label('stampa_anagrafica', 'Nel modulo di iscrizione, mostra anagrafica utente') !!}
							<p>Se selezionato, nella pagina delle iscrizioni e nel modulo d'iscrizione all'evento viene mostrata l'anagrafica dell'utente che ha eseguito l'iscrizione.</p>
							{!! Form::hidden('stampa_anagrafica', 0) !!}
							{!! Form::checkbox('stampa_anagrafica', 1, $event->stampa_anagrafica, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col" id="div_specs_iscrizione">
							{!! Form::label('spec_iscrizione', 'Quale specfica stampare nel modulo di iscrizione invece dell\'anagrafica?') !!}
							<p>Se non hai selezionato la casella qui a fianco, scegli quali informazioni dell'iscrizione mostrare al posto dei dati anagrafici.</p>
							<?php
							//seleziono tutte le specifiche generali dell'evento di tipo testo
							$specs = EventSpec::where([['id_event', $event->id], ['id_type', -1], ['general', 1]])->orderBy('id')->pluck('label', 'id');
							//$array_specifiche = json_decode($event->spec_iscrizione, JSON_FORCE_OBJECT);
							if($specs == null){
								echo "<p style='text-align: center'><b>Non hai ancora creato nessuna specifica per l'evento</b></p>";
							}else{
								foreach($specs as $key => $value){
									echo Form::checkbox('spec_iscrizione[]', $key, array_key_exists($key, $specs), ['class' => '', 'id' => 'spec_iscrizione[]'])." ".$value."<br>";
								}
							}
							?>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col" >
							{!! Form::label('image', 'Immagine') !!}<br>
							{!! Form::file('image', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('image', 'Immagine attuale') !!}<br>
							<?php
							if($event->image!=''){
								echo "<img src='".url(Storage::url('public/'.$event->image))."' width=200px/>";
							}else{
								echo "Nessuna immagine inserita!<br><br>";
							}
							?>
						</div>
					</div>

					<div class="form-row" style="min-height: 200px">
						<div class="form-group col">
							{!! Form::label('color', 'Colore') !!}
							{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
						</div>

						<div class="form-group col" style="min-height: 100px">
						</div>
					</div>



					<h3>Modulo di iscrizione</h3>

					<div class="form-row" style="min-height: 100px;">
						<div class="form-group col">
							{!! Form::label('template_file', 'Carica un template personalizzato per il modulo di iscrizione. Altrimenti verrà utilizzato quello di default.') !!}
							{!! Form::file('template_file', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							<a href="{{ url(Storage::url('public/template/subscription_template.docx')) }}">Scarica il modulo di default.</a><br>
							{!! Form::hidden('elimina_template', 0) !!}
							@if($event->template_file == null)
							<p>Nessun modulo personalizzato caricato!</p><br>
							@else
							<a href="{{ url(Storage::url('public/'.$event->template_file.'')) }}">Scarica il modulo che hai caricato.</a><br>
							{!! Form::checkbox('elimina_template', 1, 0, []) !!} Elimina modulo caricato e usa quello di default
							@endif
						</div>

						<div class="form-group col">
							{!! Form::label('pagine_foglio', 'Opzioni di stampa. Scegli se il file PDF finale da stampare deve essere composto da una o più pagine per foglio.') !!}
							{!! Form::select('pagine_foglio', Event::getPaginePerFoglio(), null, ['class' => 'form-control']) !!}
						</div>


					</div>

					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('grazie', "Messaggio finale da mostrare all'utente prima di stampare il modulo") !!}
							{!! Form::textarea('grazie', null, ['class' => 'form-control']) !!}
						</div>
					</div>


					<div class="form-group">
						<div class="form-group col">
							{!! Form::submit('Salva Evento', ['class' => 'btn btn-primary form-control']) !!}
						</div>
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
