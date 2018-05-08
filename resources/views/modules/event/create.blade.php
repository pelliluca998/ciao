<?php
use Modules\Event\Entities\Event;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1><i class="fas fa-calendar-alt" aria-hidden="true"></i> Nuovo evento</h1>
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

					{!! Form::open(['route' => 'events.store', 'files' => true]) !!}
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
							{!! Form::checkbox('active', 1, true, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
							{!! Form::label('more_subscriptions', 'Permetti iscrizioni multiple dello stesso utente') !!}
							<p>Utile, ad esempio, se un genitore deve iscrivere più figli allo stesso evento.</p>
							{!! Form::hidden('more_subscriptions', 0) !!}
							{!! Form::checkbox('more_subscriptions', 1, false, ['class' => 'form-control']) !!}
						</div>

					</div>



					<div class="form-group">
						{!! Form::label('descrizione', 'Descrizione') !!}
						{!! Form::textarea('descrizione', null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group" style="min-height: 150px">
            <div class="form-group panel-left" >
							{!! Form::label('image', 'Immagine') !!}
							{!! Form::file('image', null, ['class' => 'form-control']) !!}
						</div>

            <div class="form-group panel-right">
							{!! Form::label('color', 'Colore') !!}
							{!! Form::text('color', null, ['class' => 'form-control jscolor {hash:true, required:false}']) !!}
						</div>
					</div>



					<h4>Modulo di iscrizione</h4>
					<div class="form-group" style="min-height: 100px;">
						<div class="form-group panel-left">
							{!! Form::label('template_file', 'Carica un template personalizzato per il modulo di iscrizione. Altrimenti verrà utilizzato quello di default.') !!}
							{!! Form::file('template_file', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group panel-right">
							<a href="{{ url(Storage::url('public/template/subscription_template.docx')) }}">Scarica il modulo di default.</a><br>
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
