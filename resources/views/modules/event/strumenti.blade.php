<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
use Modules\Event\Entities\EventSpec;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1><i class="fas fa-gavel" aria-hidden="true"></i> Strumenti</h1>
		<p class="lead">Alcuni strumenti utili per il tuo evento</p>
		<hr>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
			<div class="panel panel-default panel-left">
				<div class="panel-heading">Genera Squadre</div>
				<div class="panel-body">
					<p>Con questo strumento puoi assegnare tutti gli iscritti ad un determinato numero di squadre. L'assegnamento viene fatto in modo casuale, tenendo conto delle presenze settimanali e di un vincolo che puoi decidere tu.</p>

					{!! Form::open(['route' => 'eventspecs.riempi_specifica']) !!}
					<div class="form-group">
						{!! Form::label('id_eventspec', "Seleziona la specifica che contiene l'elenco delle squadre") !!}
						{!! Form::select('id_eventspec', EventSpec::where([['id_event', Session::get('work_event')], ['event_specs.general', 1], ['event_specs.id_type', '>', 0]])->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['id' => 'pesco1', 'class' => 'form-control', 'onchange' =>  'change_type(this, "multiple", "valore1[]", "valore1", false, "span_type1")']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('id_eventspec_values', "Di questa specifica, quali valori posso utilizzare?") !!}
						<span id="span_type1"></span>
					</div>

					<div class="form-group">
						{!! Form::label('id_pesco', "Quale specifica rappresenta invece il vincolo?") !!}
						{!! Form::select('id_pesco', EventSpec::where([['id_event', Session::get('work_event')], ['event_specs.general', 1]])->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['id' => 'pesco2', 'class' => 'form-control', 'onchange' =>  'change_type(this, "multiple", "valore2[]", "valore2", false, "span_type2")']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('valore', "Quali valori può assumere il vincolo?") !!}
						<span id="span_type2"></span>
					</div>

					<div class="form-group">
						{!! Form::submit('Genera', ['class' => 'btn btn-primary form-control']) !!}
					</div>

					{!! Form::close() !!}

				</div>
			</div>

			<div class="panel panel-default panel-right">
				<div class="panel-heading">Elimina specifica da tutte le iscrizioni</div>
				<div class="panel-body">
					<p>Se per sbaglio hai generato o inserito una specifica in tutte le iscrizioni, puoi eliminarla in un colpo solo.</p>

					{!! Form::open(['route' => 'eventspecs.elimina_specifica']) !!}

					<div class="form-group">
						{!! Form::label('id_eventspec', "Specifica da eliminare dalle iscrizioni") !!}

						{!! Form::select('id_eventspec', EventSpec::where('id_event', Session::get('work_event'))->orderBy('event_specs.ordine')->pluck('event_specs.label', 'event_specs.id'), null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::submit('Elimina', ['class' => 'btn btn-primary form-control']) !!}
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
	$('#pesco1').change();
	$('#pesco2').change();
});
</script>
@endpush
