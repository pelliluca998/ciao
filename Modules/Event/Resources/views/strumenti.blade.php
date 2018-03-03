<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
use Modules\Event\Entities\EventSpec;
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
	<div class="row">
		<h1>Strumenti</h1>
		<p class="lead">Alcuni strumenti  <a href="{{ route('events.index') }}">torna all'elenco degli eventi.</a></p>
		<hr>
	</div>
	@if(Session::has('flash_message'))
	    <div class="alert alert-success">
		   {{ Session::get('flash_message') }}
	    </div>
	@endif
	<div class="row">
		<div class="">
			<div class="panel panel-default panel-left">
				<div class="panel-heading">Genera Squadre</div>
				<div class="panel-body">
					<p>Con questo strumento puoi assegnare tutti gli iscritti ad un determinato numero di squadre. L'assegnamento viene fatto in modo casuale, tenendo conto delle presenze settimanali e di un vincolo che puoi decidere tu.</p>
					
					{!! Form::open(['route' => 'eventspecs.riempi_specifica']) !!}
					
					<div class="form-group">
						{!! Form::label('id_eventspec', "Specifica, già presente nell'iscrizione, da riempire") !!}
						
						
						{!! Form::select('id_eventspec', EventSpec::where([['id_event', Session::get('work_event')], ['event_specs.general', 1], ['event_specs.id_type', '>', 0]])->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['id' => 'pesco1', 'class' => 'form-control', 'onchange' =>  'change_type(this, "multiple", "valore1[]", "valore1", false, "span_type1")']) !!}
					</div>
					
					<div class="form-group">
						{!! Form::label('id_eventspec_values', "Di questa specifica, quali valori posso utilizzare?") !!}
						<span id="span_type1"></span>
					</div>
					
					<div class="form-group">
						{!! Form::label('id_pesco', "Quale specifica rappresenta invece il vincolo?") !!}
						{!! Form::select('id_pesco', EventSpec::where([['id_event', Session::get('work_event')], ['event_specs.general', 1]])->orderBy('event_specs.id')->pluck('event_specs.label', 'event_specs.id'), null, ['id' => 'pesco2', 'class' => 'form-control', 'onchange' =>  'change_type(this, "multiple", "valore2[]", "valore2", false, "span_type2")']) !!}
					</div>
					
					<div class="form-group">
						{!! Form::label('valore', "Quali valori può assumere il vincolo?") !!}					
						
						<span id="span_type2"></span>
					</div>
					
					<div class="form-group">
						{!! Form::submit('Genera', ['class' => 'btn btn-primary form-control']) !!}
					</div>				
				
		      		{!! Form::close() !!}
		      		
				</div> <!-- end body-->
			</div>
			
			<div class="panel panel-default panel-right">
				<div class="panel-heading">Elimina specifica da tutte le iscrizioni</div>
				<div class="panel-body">
					<p>Se per sbaglio hai generato o inserito una specifica in tutte le iscrizioni, puoi eliminarla in un colpo solo.</p>
					
					{!! Form::open(['route' => 'eventspecs.elimina_specifica']) !!}
					
					<div class="form-group">
						{!! Form::label('id_eventspec', "Specifica da eliminare dalle iscrizioni") !!}					
						
						{!! Form::select('id_eventspec', EventSpec::where('id_event', Session::get('work_event'))->orderBy('event_specs.ordine')->pluck('event_specs.label', 'event_specs.id'), null, ['class' => 'form-control']) !!}
					</div>
					
					<div class="form-group">
						{!! Form::submit('Elimina', ['class' => 'btn btn-primary form-control']) !!}
					</div>				
				
		      		{!! Form::close() !!}
				</div>
				
			</div>
				
				
		</div>
	</div>
</div>



<script>
$(document).ready(function(){
	$('#pesco1').change();
	$('#pesco2').change();
});
</script>

@endsection
