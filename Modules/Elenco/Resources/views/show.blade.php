<?php
use App\Elenco;
use App\ElencoValue;
use App\Role;
use App\Permission;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\IdFieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use Nayjest\Grids\ObjectDataRow;
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
        <div class="">
		<div class="panel panel-default">
		<div class="panel-heading">Elenco <b>{{$elenco->nome}}</b></div>
		<div class="panel-body">
		
			<?php
				$colonne = json_decode($elenco->colonne, true);
				$keys = array_keys($colonne);
				$values = ElencoValue::select('users.id as id_user', 'users.cognome', 'users.name', 'elenco_values.id', 'elenco_values.valore')
					->leftJoin('users', 'users.id', 'elenco_values.id_user')
					->where('id_elenco', $elenco->id)
					->orderBy('users.cognome', 'ASC')
					->get();
				$index = -1;
			?>
			{!! Form::open(['route' => 'elenco.save_values']) !!}
				{!! Form::hidden("id_elenco", $elenco->id) !!}			
				<table class="testgrid" id="elenco_values">
					<thead>
						<tr>
							<th>#</th>
							<th>Utente</th>
						
							@foreach($colonne as $c)
								<th>{{$c}}</th>
							@endforeach
							<th style="width: 5%;">Elimina</th>
						</tr>
					</thead>
					@foreach($values as $v)
						{!! Form::hidden("id_values[".$loop->index."]", $v->id) !!}
						<tr id="row_{{$v->id}}">
							<td>{{$v->id}}</td>
							<td>{!! Form::hidden("id_user[".$loop->index."]", $v->id_user) !!} {{$v->cognome}} {{$v->name}}</td>
							<?php
								$val = json_decode($v->valore, true);
								$index = $loop->index;
							?>
							@foreach($colonne as $c)
								<td>
								<?php
									$check = 0;
									if(isset($val[$keys[$loop->index]])){
										$check = $val[$keys[$loop->index]];
									}
								?>
								{!! Form::hidden("colonna[".$index."][".$keys[$loop->index]."]", 0) !!}
				          		{!! Form::checkbox("colonna[".$index."][".$keys[$loop->index]."]", 1, $check, ['class' => 'form-control']) !!}
								</td>
							@endforeach
							<td>
								<button onclick="elencovalue_destroy({{$v->id}})" style="font-size: 15px;" type='button' class="btn btn-primary btn-sm" ><i class="fa fa-trash fa-2x" aria-hidden="true"></i></button>
							</td>
						</tr>
					@endforeach
				</table>
				<br>
				<input id='contatore' type='hidden' value="{{$index}}" />
				{!! Form::submit('Salva valori', ['class' => 'btn btn-primary form-control', 'style' => 'width: 33%']) !!} <button onclick="elencovalues_add({{count($colonne)}}, '{{json_encode($keys)}}')" style="font-size: 15px; width: 33%; margin-right: 2%" type='button' class="btn btn-primary btn-sm" ><i class="fa fa-plus" aria-hidden="true"></i>Aggiungi riga</button>
           	{!! Form::close() !!} 
                   
           </div>
           </div>
        </div>
    </div>
</div>

@endsection
