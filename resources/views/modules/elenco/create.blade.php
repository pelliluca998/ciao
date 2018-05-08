<?php
use Modules\Elenco\Entities\Elenco;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Nuovo Elenco</h1>
<p class="lead">Inserisci le informazioni per il nuovo elenco qui sotto, oppure <a href="{{ route('elenco.index') }}">torna alla lista completa.</a></p>
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
			{!! Form::open(['route' => 'elenco.store']) !!}
				{!! Form::hidden('id_event', Session::get('work_event')) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>			
				
				
				<div class="form-group">
				{!! Form::submit('Salva Elenco', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
