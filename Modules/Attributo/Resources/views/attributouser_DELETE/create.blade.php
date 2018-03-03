<?php
use Modules\Attributo\Entities\Attributo;
use Modules\Attributo\Entities\AttributoUser;
use App\Type;
?>

@extends('layouts.app')

@section('content') 

<div class="container">
    <div class="row">
        <h1>Nuovo attributo utente</h1>
        <p class="lead">Inserisci le informazioni per il nuovo attributo qui sotto, oppure <a href="{{ route('attributouser.show') }}?id_user={{$id_user}}">torna all'elenco completo.</a></p>
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
			{!! Form::open(['route' => 'attributouser.store']) !!}				
				<div class="form-group">
				{!! Form::label('id_attributo', 'Attributo') !!}
				{!! Form::select('id_attributo', Attributo::where('id_oratorio', Session::get('session_oratorio'))->orderBy('ordine')->pluck('nome', 'id'), null, ['class' => 'form-control', 'onchange' => 'change_attributo_type(this)']) !!}
				</div>

               <div class="form-group">
				{!! Form::label('valore', 'Valore') !!}
				<span id="attrib_value"></span>
				</div>

				{!! Form::hidden('id_user', $id_user) !!}
				
				<div class="form-group">
				{!! Form::submit('Salva Informazione', ['class' => 'btn btn-primary form-control']) !!}
				</div>			
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
