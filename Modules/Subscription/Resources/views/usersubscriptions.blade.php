<?php
use App\Subscription;
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
<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default panel-left">
		<div class="panel-heading">Le tue iscrizioni</div>
		<div class="panel-body">			
			<?php
			$query = Input::query(); //sono i parametri GET della tabella
			Session::put('query_param', $query);
			if(!isset($id_event) || $id_event==null){
				$id_event=Session::get('work_event');
			}
			$query = (new Subscription)
			    ->select('subscriptions.id', 'subscriptions.confirmed', 'events.nome', 'events.id as id_event')
			    ->leftJoin('events', 'events.id', '=', 'subscriptions.id_event')
			    ->where('subscriptions.id_user', '=', Auth::user()->id)->get();
			?>
			<table class='testgrid'>
				<thead><tr>
				<th>ID</th>
				<th>Evento</th>
				<th>Approvata?</th>
				<th>Cancella</th>
				<th>Stampa</th>
				<th>Dettagli</th>
				</tr></thead>
				@foreach($query as $sub)
					<tr>
					<td>{{$sub->id}}</td>
					<td>{{$sub->nome}}</td>
					@if($sub->confirmed==1)
						<td><i class="fa fa-check-square-o fa-2x" aria-hidden='true'></i></td>
						<td><i class="fa fa-trash fa-2x" aria-hidden="true" style="cursor: unset;"></i></td>
					@else
						<td><i class="fa fa-square-o fa-2x" aria-hidden='true'></i></td>
						<td><a href="{{route('subscription.destroy', ['id_sub' => $sub->id])}}"><i class="fa fa-trash fa-2x" aria-hidden="true" style="cursor: unset;"></i></a></td>
					@endif
					<td><a href="{{url('subscription/print')}}?id_subscription={{$sub->id}}"><i class="fa fa-print fa-2x" aria-hidden='true'></i></a></td>
					<td><i style="color:#3e93c3" onclick="load_spec_usersubscription({{$sub->id}}, {{$sub->id_event}})" class="fa fa-flag fa-2x" aria-hidden="true"></i></td>
					</tr>
				@endforeach
			</table>
                   
                </div>
            </div>
            
		<div class="panel-right">
		<div class="panel panel-default">
			<div class="panel-heading">Dettagli dell'iscrizione - Parte 1</div>
			<div id="spec1" class="panel-body">
			 
			</div>
		</div>
		</div>
    </div>
</div>
       
        
<?php

?>
@endsection
