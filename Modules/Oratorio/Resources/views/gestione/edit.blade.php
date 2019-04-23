<?php
use Modules\Oratorio\Entities\Oratorio;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Oratorio <i>{{$oratorio->nome}}</i></h1>
		<hr>
	</div>
	@if(Session::has('flash_message'))
	<div class="alert alert-success">
		{{ Session::get('flash_message') }}
	</div>
	@endif
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::model($oratorio, ['method' => 'PATCH','files' => true, 'route' => ['oratorioowner.update', $oratorio->id]]) !!}
					{!! Form::hidden('id_oratorio', $oratorio->id) !!}
					<div class="form-group">
						{!! Form::label('nome', 'Nome oratorio') !!}
						{!! Form::text('nome', null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('email', 'Indirizzo Email') !!}
						{!! Form::text('email', null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('sms_sender', 'Mittente SMS. Puoi inserire un numero di cellulare (con prefisso internazionale senza + iniziale) oppure un nome (max. 11 caratteri).') !!}
						{!! Form::text('sms_sender', null, ['class' => 'form-control', 'maxlength' => '12']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('reg_visible', 'Nome oratorio visibile nella pagina di registrazione utente') !!}
						{!! Form::hidden('reg_visible', 0) !!}
						{!! Form::checkbox('reg_visible', 1, $oratorio->reg_visible, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						<div class="form-group" style="width: 48%; float: left;">
							{!! Form::label('logo', 'Logo') !!}
							{!! Form::file('logo', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group" style="width: 48%; float: left;">
							Logo attuale:<br>
							<?php
							if($oratorio->logo!=''){
								echo "<img src='".url(Storage::url('public/'.$oratorio->logo))."' width=200px/>";
							}else{
								echo "Nessun logo ancora caricato!<br><br>";
							}
							?>
						</div>

					</div>					



					<div class="form-group">
						{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control']) !!}
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
function insert_date(checkbox, loop){
	if(checkbox.checked){
		var data_inizio = moment();
	  var data_inizio = moment(moment(), 'DD/MM/YYYY');
		$('#data_inizio_'+loop).val(data_inizio.format('DD/MM/YYYY'));
	  var data_fine = data_inizio.add('years', 1);
		$('#data_fine_'+loop).val(data_fine.format('DD/MM/YYYY'));

	}
}
</script>
@endpush
