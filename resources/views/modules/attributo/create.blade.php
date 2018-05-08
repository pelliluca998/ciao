<?php
use Modules\Attributo\Entities\Attributo;
use Modules\Oratorio\Entities\Type;
?>

@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row">
		<h1>Nuovo Attributo</h1>
		<p class="lead">Inserisci le informazioni per il nuovo attributo qui sotto, oppure <a href="{{ route('attributo.index') }}">torna all'elenco completa.</a></p>
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
			{!! Form::open(['route' => 'attributo.store']) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome Attributo') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
				</div>
            
                <div class="form-group">
				{!! Form::label('ordine', 'Ordine') !!}
				{!! Form::number('ordine', '1', ['class' => 'form-control']) !!}
				</div>
            
                <div class="form-group">
				{!! Form::label('note', 'Note') !!}
				{!! Form::text('note', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_type', 'Tipo Attributo') !!}
				{!! Form::select('id_type', Type::getTypes(), null, ['class' => 'form-control']) !!}
				</div>

                <div class="form-group">
				{!! Form::label('hidden', 'Nascosto') !!}
				{!! Form::hidden('hidden', 0) !!}
				{!! Form::checkbox('hidden', 1, 0, ['class' => 'form-control']) !!}
				</div>

				{!! Form::hidden('id_event', Session::get('work_event')) !!}

				<div class="form-group">
				{!! Form::submit('Salva Attributo', ['class' => 'btn btn-primary form-control']) !!}
				</div>

           		{!! Form::close() !!}
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
