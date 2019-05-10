<?php
use Modules\Oratorio\Entities\Oratorio;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col">
			<div class="card bg-transparent border-0">
				<h1><i class='fas fa-cube'></i> Il tuo Oratorio</h1>
				<p class="lead">Alcune informazioni circa il tuo oratorio</p>
				<hr>
			</div>
		</div>
	</div>

	<div class="row justify-content-center" style="margin-top: 20px;">
		<div class="col-8">
			<div class="card">
				<div class="card-body">

					@if(Session::has('flash_message'))
					<div class="alert alert-success">
						{{ Session::get('flash_message') }}
					</div>
					@endif

					{!! Form::model($oratorio, ['method' => 'PATCH','files' => true, 'route' => ['oratorio.update', $oratorio->id]]) !!}
					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('nome', 'Nome oratorio') !!}
							{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('email', 'Indirizzo Email') !!}
							{!! Form::text('email', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('nome_parrocchia', 'Nome parrocchia') !!}
							{!! Form::text('nome_parrocchia', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('indirizzo_parrocchia', 'Indirizzo parrocchia') !!}
							{!! Form::text('indirizzo_parrocchia', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('nome_diocesi', 'Nome della Diocesi') !!}
							{!! Form::text('nome_diocesi', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
							{!! Form::label('luogo_firma_moduli', 'Luogo di firma dei moduli') !!}
							{!! Form::text('luogo_firma_moduli', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col">
							{!! Form::label('logo', 'Logo') !!}
							{!! Form::file('logo', null, ['class' => 'form-control']) !!}
						</div>

						<div class="form-group col">
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

					<div class="form-row">
						<div class="form-group col">
							{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control']) !!}
						</div>
					</div>
					{!! Form::close() !!}

				</div>
			</div>

		</div>
	</div>
</div>
@endsection
