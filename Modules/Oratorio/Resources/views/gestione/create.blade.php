<?php
use App\LicenseType;
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

        <h2>Gestione Licenza e moduli</h2>
        <table class="testgrid">
          <tr><thead><th>Modulo</th><th>Data aquisto</th><th>Data scadenza</th></thead></tr>
          @foreach(Module::all() as $module)
          <tr>
            {!! Form::hidden('id_licenza['.$loop->index.']', null) !!}
            {!! Form::hidden('module_name['.$loop->index.']', $module->getLowerName()) !!}
            {!! Form::hidden('abilita['.$loop->index.']', 0) !!}
            <td>{!! Form::checkbox('abilita['.$loop->index.']', 1, false, ['onchange' => 'insert_date(this, '.$loop->index.')']) !!} {{$module->getName()}}</td>
            <td>{!! Form::text('data_inizio['.$loop->index.']', null, ['class' => 'form-control data', 'id' => 'data_inizio_'.$loop->index]) !!}</td>
            <td>{!! Form::text('data_fine['.$loop->index.']', null, ['class' => 'form-control data', 'id' => 'data_fine_'.$loop->index]) !!}</td>
          </tr>
          @endforeach
        </table>

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
