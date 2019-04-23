<?php
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Nuovo Oratorio</h1>
<p class="lead">Inserisci le informazioni per il nuovo oratorio qui sotto, oppure <a href="{{ route('oratorio.showall') }}">torna all'elenco completo.</a></p>
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
			{!! Form::open(['route' => 'oratorio.store']) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('email', 'Email') !!}
				{!! Form::text('email', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('id_user', "Utente amministratore") !!}
				{!! Form::select('id_user', User::orderBy('email', 'ASC')->pluck('email', 'id'), null , ['class' => 'form-control'])!!}
				</div>        

			</div>




				<div class="form-group">
				{!! Form::submit('Salva oratorio', ['class' => 'btn btn-primary form-control']) !!}
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
