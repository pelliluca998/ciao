<?php
use Modules\User\Entities\User;
use App\Type;
//use Session;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Modifica attributo</h1>
		<p class="lead">Modifica e salva l'attributo qui sotto, oppure <a href="{{ route('attributo.index') }}">torna all'elenco completo.</a></p>
		<hr>
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-heading">Informazioni aggiuntive utenti</div>
		<div class="panel-body">	
			{!! Form::model($attributo, ['method' => 'PATCH','route' => ['attributo.update', $attributo->id]]) !!}
				{!! Form::hidden('id_attributo', $attributo->id) !!}
				<div class="form-group">
				{!! Form::label('nome', 'Nome Attributo') !!}
				{!! Form::text('nome', null, ['class' => 'form-control']) !!}
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
				{!! Form::label('ordine', 'Ordine') !!}
				{!! Form::number('ordine', null, ['class' => 'form-control']) !!}
				</div>
            
                <?php
				$checked=false;
				if ($attributo->hidden==1) $checked=true;
				?>
                <div class="form-group">
				{!! Form::label('hidden', 'Nascosto') !!}
				{!! Form::hidden('hidden', 0) !!}
				{!! Form::checkbox('hidden', 1, $attributo->hidden, ['class' => 'form-control']) !!}
				</div>
				
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
