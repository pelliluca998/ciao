<?php
use App\Event;
use App\EventSpec;
use App\Cassa;
use App\ModoPagamento;
use App\TipoPagamento;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1>Configurazione contabilità</h1>
		<p class="lead">Aggiungi o modifica le casse, le modalità e i tipi di pagamento.</a></p>
		<hr>
	</div>
	@if(Session::has('flash_message'))
	    <div class="alert alert-success">
		   {{ Session::get('flash_message') }}
	    </div>
	@endif
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-body">
					<h2>Casse</h2>
					<?php
					$cassa = Cassa::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->get();
					$index = 0;
					?>
					{!! Form::open(['route' => 'contabilita.save_casse']) !!}
					<table class="testgrid" id="table_casse">
					<thead>
						<tr>
							<th>ID</th>
							<th>Nome</th>
							<th>Default</th>
							<th>Del</th>
						</tr>
					</thead>
					@foreach($cassa as $c)
						<tr>
						<td>{!! Form::hidden('id['.$loop->index.']', $c->id) !!}{{$c->id}}</td>
						<td>{!! Form::text('label['.$loop->index.']', $c->label, ['class' => 'form-control']) !!}</td>
						<td>{!! Form::radio('as_default', $c->id, $c->as_default, ['class' => 'form-control']) !!}</td>
						<td><a href="{{url('admin/contabilita/cassa', [$c->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
						</tr>
						@php
							$index=$loop->index+1;
						@endphp
					@endforeach
					</table>
					<br>
					<input id='contatore_c' type='hidden' value="{{$index}}" />
					{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 49%']) !!}
		 			<i onclick='add_cassa();' class="btn btn-primary" style='width: 45%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi Cassa</i>
					{!! Form::close() !!}
   				</div>
   				
   				<div class="panel-body">
					<h2>Modalità pagamento</h2>
					<?php
					$modo = ModoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->get();
					$index = 0;
					?>
					{!! Form::open(['route' => 'contabilita.save_modo']) !!}
					<table class="testgrid" id="table_modo">
					<thead>
						<tr>
							<th>ID</th>
							<th>Nome</th>
							<th>Default</th>
							<th>Del</th>
						</tr>
					</thead>
					@foreach($modo as $c)
						<tr>
						<td>{!! Form::hidden('id['.$loop->index.']', $c->id) !!}{{$c->id}}</td>
						<td>{!! Form::text('label['.$loop->index.']', $c->label, ['class' => 'form-control']) !!}</td>
						<td>{!! Form::radio('as_default', $c->id, $c->as_default, ['class' => 'form-control']) !!}</td>
						<td><a href="{{url('admin/contabilita/modo', [$c->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
						</tr>
						@php
							$index=$loop->index+1;
						@endphp
					@endforeach
					</table>
					<br>
					<input id='contatore_m' type='hidden' value="{{$index}}" />
					{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 49%']) !!}
		 			<i onclick='add_modo_pagamento();' class="btn btn-primary" style='width: 45%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi Modalità pagamento</i>
					{!! Form::close() !!}
   				</div>
   				
   				
   				<div class="panel-body">
					<h2>Tipologia pagamento</h2>
					<?php
					$tipo = TipoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->get();
					$index = 0;
					?>
					{!! Form::open(['route' => 'contabilita.save_tipo']) !!}
					<table class="testgrid" id="table_tipo">
					<thead>
						<tr>
							<th>ID</th>
							<th>Nome</th>
							<th>Default</th>
							<th>Del</th>
						</tr>
					</thead>
					@foreach($tipo as $c)
						<tr>
						<td>{!! Form::hidden('id['.$loop->index.']', $c->id) !!}{{$c->id}}</td>
						<td>{!! Form::text('label['.$loop->index.']', $c->label, ['class' => 'form-control']) !!}</td>
						<td>{!! Form::radio('as_default', $c->id, $c->as_default, ['class' => 'form-control']) !!}</td>
						<td><a href="{{url('admin/contabilita/tipo', [$c->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
						</tr>
						@php
							$index=$loop->index+1;
						@endphp
					@endforeach
					</table>
					<br>
					<input id='contatore_t' type='hidden' value="{{$index}}" />
					{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 49%']) !!}
		 			<i onclick='add_tipo_pagamento();' class="btn btn-primary" style='width: 45%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi tipologia pagamento</i>
					{!! Form::close() !!}
   				</div>
			</div>
		</div>
	</div>
</div>
@endsection
