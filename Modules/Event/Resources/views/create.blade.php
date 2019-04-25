<?php
use Modules\Event\Entities\Event;
use Modules\Modulo\Entities\Modulo;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col">
			<div class="card bg-transparent border-0">
				<h1><i class='fas fa-calendar-alt'></i> Eventi</h1>
				<p class="lead">Crea un nuovo evento</p>
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

					{!! Form::open(['route' => 'events.store_event', 'files' => true]) !!}
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
							{!! Form::checkbox('active', 1, true, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
							<p>Utile, ad esempio, se un genitore deve iscrivere più figli allo stesso evento.</p>
							{!! Form::hidden('more_subscriptions', 0) !!}
							{!! Form::checkbox('more_subscriptions', 1, false, ['class' => 'form-control']) !!}
						</div>

						@if(Module::find('famiglia') != null && Module::find('famiglia')->enabled())
						<div class="form-group col">
							{!! Form::label('select_famiglia', 'Richiedi l\'iscrizione per un membro della famiglia') !!}
							{!! Form::hidden('select_famiglia', 0) !!}
							<p>Se selezionato, in fase d'iscrizione verrà richiesto di selezionare un membro della famiglia dell'utente</p>
							{!! Form::checkbox('select_famiglia', 1, null, ['class' => 'form-control']) !!}
						</div>
						@else
						{!! Form::hidden('select_famiglia', 0) !!}
						@endif

						@if(Module::find('diocesi') != null && Module::find('diocesi')->enabled() && Auth::user()->can('add-events-diocesi'))
						<div class="form-group col">
							{!! Form::label('is_diocesi', 'Crea l\'evento come Diocesi e non come parrocchia') !!}
							{!! Form::hidden('is_diocesi', 0) !!}
							{!! Form::checkbox('is_diocesi', 1, null, ['class' => 'form-control']) !!}
						</div>
						@else
						{!! Form::hidden('is_diocesi', 0) !!}
						@endif

					</div>



					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('descrizione', 'Descrizione') !!}
							{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row" style="min-height: 150px">
						<div class="form-group col" >
							{!! Form::label('image', 'Immagine') !!}
							{!! Form::file('image', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('color', 'Colore') !!}
							{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
						</div>
					</div>



					<h3>Modulo d'iscrizione</h3>
					<div class="form-row" >
						<div class="form-group col">
							{!! Form::label('id_modulo', 'Seleziona uno o più moduli da generare al termine dell\'iscrizione') !!}

							<table class="table table-bordered" id="moduloTable" style="width: 100%">
		            <thead>
		              <tr>
		                <th style="width: 10%">Seleziona</th>
		                <th>Nome modulo</th>
		              </tr>
		            </thead>
								<tbody>
									@foreach(Modulo::where('id_oratorio', Session::get('session_oratorio'))->orderBy('label', 'ASC')->get() as $modulo)
									<tr>
										<td>{!! Form::checkbox('id_modulo[]', $modulo->id, false, ['class' => 'form-control']) !!}</td>
										<td>{{ $modulo->label }}</td>
									</tr>
									@endforeach
								</tbody>
		          </table>
						</div>

						<div class="form-group col">
							{!! Form::label('pagine_foglio', 'Opzioni di stampa. Scegli se il file PDF finale da stampare deve essere composto da una o più pagine per foglio.') !!}
							{!! Form::select('pagine_foglio', Event::getPaginePerFoglio(), null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
						</div>
					</div>


					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('grazie', "Messaggio finale da mostrare all'utente prima di stampare il modulo") !!}
							{!! Form::textarea('grazie', null, ['class' => 'form-control']) !!}
						</div>
					</div>


					<div class="form-row">
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
