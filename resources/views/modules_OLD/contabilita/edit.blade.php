<?php
use App\Event;
use App\Cassa;
use App\ModoPagamento;
use App\TipoPagamento;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Modifica voce di bilancio</h1>
<p class="lead">Modifica le informazioni qui sotto, oppure <a href="{{ route('contabilita.index') }}">torna all'elenco completo.</a></p>
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
			{!! Form::model($bilancio, ['method' => 'PATCH', 'route' => ['contabilita.update', $bilancio->id]]) !!}
				{!! Form::hidden('id', $bilancio->id) !!}
				<div class="form-group">
				{!! Form::label('id_tipopagamento', 'Tipologia pagamento') !!}
				{!! Form::select('id_tipopagamento', TipoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('label', 'ASC')->pluck('label', 'id'), $bilancio->id_tipopagamento, ['class' => 'form-control'])!!}
				</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::text('descrizione', $bilancio->descrizione, ['class' => 'form-control']) !!}
				</div>
            
                	<div class="form-group">
				{!! Form::label('id_modalita', 'Modalità pagamento') !!}
				{!! Form::select('id_modalita', ModoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('label', 'ASC')->pluck('label', 'id'), $bilancio->id_modalita, ['class' => 'form-control'])!!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_cassa', 'Cassa') !!}
				{!! Form::select('id_cassa', Cassa::where('id_oratorio', Session::get('session_oratorio'))->orderBy('label', 'ASC')->pluck('label', 'id'), $bilancio->id_cassa, ['class' => 'form-control'])!!}
				</div>
            
                	<div class="form-group">
				{!! Form::label('importo', 'Importo (€)') !!}
				{!! Form::number('importo', $bilancio->importo, ['step' => '0.01', 'class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('data', 'Data') !!}
				{!! Form::text('data', $bilancio->data, ['class' => 'form-control','id' => 'datepicker']) !!}
				</div>	
				
				
				<div class="form-group">
				{!! Form::submit('Salva voce di bilancio', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
