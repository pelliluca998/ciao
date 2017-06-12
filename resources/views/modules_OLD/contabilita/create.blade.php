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
<h1>Nuovo voce di bilancio</h1>
<p class="lead">Inserisci le informazioni qui sotto, oppure <a href="{{ route('contabilita.index') }}">torna all'elenco completo.</a></p>
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
			{!! Form::open(['route' => 'contabilita.store']) !!}
				{!! Form::hidden('id_user', Auth::user()->id) !!}
				{!! Form::hidden('id_event', Session::get('work_event')) !!}
				{!! Form::hidden('id_eventspecvalues', 0) !!}
				<div class="form-group">
				{!! Form::label('id_tipopagamento', 'Tipologia pagamento') !!}
				{!! Form::select('id_tipopagamento', TipoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('as_default', 'DESC')->orderBy('label', 'ASC')->pluck('label', 'id'), '', ['class' => 'form-control'])!!}
				</div>
				
				<div class="form-group">
				{!! Form::label('descrizione', 'Descrizione') !!}
				{!! Form::text('descrizione', null, ['class' => 'form-control']) !!}
				</div>
            
                	<div class="form-group">
				{!! Form::label('id_modalita', 'Modalità pagamento') !!}
				{!! Form::select('id_modalita', ModoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('as_default', 'DESC')->orderBy('label', 'ASC')->pluck('label', 'id'), '', ['class' => 'form-control'])!!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_cassa', 'Cassa') !!}
				{!! Form::select('id_cassa', Cassa::where('id_oratorio', Session::get('session_oratorio'))->orderBy('as_default', 'DESC')->orderBy('label', 'ASC')->pluck('label', 'id'), '', ['class' => 'form-control'])!!}
				</div>
            
                	<div class="form-group">
				{!! Form::label('importo', 'Importo (€)') !!}
				{!! Form::number('importo', 0, ['step' => '0.01', 'class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('data', 'Data') !!}
				{!! Form::text('data', null, ['class' => 'form-control','id' => 'datepicker']) !!}
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
