<?php
use Modules\User\Entities\User;
use Modules\User\Entities\Group;
use Modules\Attributo\Entities\Attributo;
use Modules\Oratorio\Entities\TypeSelect;
?>

@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row">
		<h1>Nuovo Utente</h1>
	<p class="lead">Inserisci le informazioni per il nuovo utente qui sotto, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
	<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<p>{{ $error }}</p>
				@endforeach
			</div>
			@endif
			{!! Form::open(['route' => 'user.store', 'files' => true]) !!}
				<div class="form-group">
				{!! Form::label('name', 'Nome') !!}
				{!! Form::text('name', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('cognome', 'Cognome') !!}
				{!! Form::text('cognome', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('sesso', 'Sesso') !!}
				<select id="sesso" class="form-control" name="sesso"><option value="M">Uomo</option><option value="F">Donna</option></select>
				</div>

				<div class="form-group">
				{!! Form::label('nato_il', 'Data di Nascita (nel formato GG/MM/AAAA)') !!}
				{!! Form::text('nato_il', '', ['class' => 'form-control', 'id' => 'datepicker']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('nato_a', 'Luogo di Nascita') !!}
				{!! Form::text('nato_a', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('residente', 'Residente a') !!}
				{!! Form::text('residente', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('via', 'Indirizzo') !!}
				{!! Form::text('via', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group" style="width: 100%; float: left;">
					{!! Form::label('email', 'Email') !!}
					<div style="width: 100%">
						<div style="width: 76%; float: left; margin-right: 3%">
							{!! Form::text('email', null, ['class' => 'form-control']) !!}
						</div>
						<div style="width: 20%; float: left;">
							<input name="genera_email" value="1" type="checkbox" onchange="disable_email(this)"/> Genera Email
						</div>
					</div>
				</div>

				<div class="form-group">
				{!! Form::label('cell_number', 'Numero cellulare') !!}
				{!! Form::text('cell_number', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group" style="width: 100%; float: left;">
				{!! Form::label('password', 'Password') !!}
					<div style="width: 100%">
						<div style="width: 76%; float: left; margin-right: 3%">
							{!! Form::password('password', null, ['class' => 'form-control']) !!}
						</div>
						<div style="width: 20%; float: left;">
							<input name="genera_password" value="1" type="checkbox" onchange="disable_password(this)"/> Genera Password
						</div>
					</div>
				</div>

				<div class="form-group">
				{!! Form::label('username', 'Username') !!}
				{!! Form::text('username', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
					<div class="form-group" style="width: 100%; float: left;">
						{!! Form::label('photo', 'Foto Profilo') !!}
						{!! Form::file('photo', null, ['class' => 'form-control']) !!}
					</div>

				</div>

				<?php
					$attributi = Attributo::where('id_oratorio', Session::get('session_oratorio'))->orderBy('ordine', 'ASC')->get();
				?>
				<h2>Informazioni aggiuntive</h2>
				@foreach($attributi as $a)
					<div class="form-group">
					{!! Form::hidden('id_attributo['.$loop->index.']', $a->id) !!}
					{!! Form::label('a['.$loop->index.']', $a->nome) !!}
					@if($a->id_type>0)
						{!! Form::select('attributo['.$loop->index.']', TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control'])!!}
					@else
						@if($a->id_type==-1)
							{!! Form::text('attributo['.$loop->index.']', null, ['class' => 'form-control']) !!}
						@elseif($a->id_type==-2)
							{!! Form::hidden('attributo['.$loop->index.']', 0) !!}
							{!! Form::checkbox('attributo['.$loop->index.']', 1, '', ['class' => 'form-control']) !!}
						@elseif($a->id_type==-3)
							{!! Form::number('attributo['.$loop->index.']', null, ['class' => 'form-control']) !!}
						@endif
					@endif
					</div>
				@endforeach


				<div class="form-group">
					{!! Form::submit('Salva Utente', ['class' => 'btn btn-primary form-control']) !!}
				</div>


           		{!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function disable_email(check){
	$('#email').prop('disabled', check.checked);
	$('#username').prop('disabled', check.checked);
}

function disable_password(check){
	$('#password').prop('disabled', check.checked);
}
</script>
@endsection
