<?php
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpec;
?>

@extends('layouts.app')

@section('content')
<div class="container">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
	<div class="row">
		<h1>Modifica Elenco</h1>
		<p class="lead">Modifica e salva l'elenco qui sotto, oppure <a href="{{ route('elenco.index') }}">torna alla lista degli elenchi.</a></p>
		<hr>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-body">	
					{!! Form::model($elenco, ['method' => 'PATCH', 'route' => ['elenco.update', $elenco->id]]) !!}
						{!! Form::hidden('id_elenco', $elenco->id) !!}
						<div class="form-group">
						{!! Form::label('nome', 'Nome') !!}
						{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>
				
						<h3>Colonne dell'elenco</h3>
						<p>Aggiungi nella tabella qui sotto le colonne che deve avere il tuo elenco</p>
						<table class="testgrid" id="colonne_elenco">
							<tr><th>Colonne</th></tr>
							<?php
								$colonne = json_decode($elenco->colonne, true);
								$keys = array_keys($colonne);
								if(count($keys)>0){
									$max = max($keys);
								}else{
									$max = 0;
								}
								
								$index=0;
							?>
							@foreach($colonne as $c)
								<tr><td>{!! Form::text('colonna['.$keys[$loop->index].']', $c, ['class' => 'form-control']) !!}</td></tr>
								@php
									$index=$loop->index+1;
								@endphp
							@endforeach
						</table>
						<input id='contatore' type='hidden' value="{{$max}}" />
						<br>
				
						<div class="form-group">
						<button onclick="colonneelenco_add()" style="font-size: 15px; width: 48%; margin-right: 2%" type='button' class="btn btn-primary btn-sm" ><i class="fa fa-plus" aria-hidden="true"></i>Aggiungi colonna</button>{!! Form::submit('Salva Elenco', ['class' => 'btn btn-primary form-control', 'style' => 'width: 48%']) !!}
						</div>				
				
		      		{!! Form::close() !!}   
   				</div>
			</div>
		</div>
	</div>
</div>
@endsection
