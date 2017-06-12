<?php
use App\SmsCredit;
use App\Role;
use App\Permission;
use Modules\Sms\Http\Controllers\SmsController;
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
			<p>Qui sotto trovi il credito residuo che hai per inviare SMS e i pacchetti acquistati. Per inviare SMS, vai in Anagrafica, scegli gli utenti e clicca su 'Invia SMS'.</p>
		<div class="panel panel-default" style="width: 50%; float: left;">
		<div class="panel-heading">Pacchetti SMS acquistati</div>
		<div class="panel-body">
			<?php
			$query = Input::query(); //sono i parametri GET della tabella
			Session::put('query_param', $query);
			$query = (new SmsCredit)
			    ->newQuery()
			    ->select()
			    ->where('sms_credits.id_oratorio', '=', Session::get('session_oratorio'));
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('smscredit_report')
			# See all supported data providers in sources
			->setDataProvider(new EloquentDataProvider($query))
			# Setup caching, value in minutes, turned off in debug mode
			->setCachingTime(5) 
			# Setup table columns
			->setColumns([
           			# simple results numbering, not related to table PK or any obtained data
            			(new FieldConfig)
                		->setName('id')
				->setLabel('ID')
				->setSortable(true),
            			(new FieldConfig)
                		->setName('data')
				->setLabel('Data Acquisto')
				->addFilter(
				    (new FilterConfig)
				        ->setName('data')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('price')
				->setLabel('Prezzo')
				->setSortable(true)
				->setCallback(function ($val, ObjectDataRow $row) {
					$sub = $row->getSrc();
					return $sub->price." €";
                        	}),
				(new FieldConfig)
                		->setName('credit')
				->setLabel('Credito')
				->setSortable(true),				


        		])
			# Setup additional grid components
			->setComponents([
				# Renders table header (table>thead)				
				(new THead)
                		# Setup inherited components
               			->setComponents([
               				(new ColumnHeadersRow),
					# Add this if you have filters for automatic placing to this row
					new FiltersRow,
					# Row with additional controls
					(new OneCellRow)
				        ->setComponents([
						# Control for specifying quantity of records displayed on page
						(new RecordsPerPage)
							->setVariants([
							50,
							100,
							1000
							])
						,
						# Control to show/hide rows in table
						(new ColumnsHider)
							->setHiddenByDefault([
						   	'created_at',
							])
						,
						# Submit button for filters. 
						# Place it anywhere in the grid (grid is rendered inside form by default).
						(new HtmlTag)
                                			->setTagName('button')
                                			->setAttributes([
		                            			'type' => 'submit',
		                            			# Some bootstrap classes
		                            			'class' => 'btn btn-primary'
                               			 	])
                                		->setContent('Filtra')
					])
					# Components may have some placeholders for rendering children there.
					->setRenderSection(THead::SECTION_BEGIN)
				]),
				# Renders table footer (table>tfoot)
				(new TFoot)
				->addComponent(
					# Renders row containing one cell 
					# with colspan attribute equal to the table columns count
					(new OneCellRow)
					# Pagination control
					->addComponent(new Pager)
                		)
			])
			);

	echo $grid->render();
			?>
           		
                   
                </div>
            </div>
            
		<div style="width: 45%; float: left; margin-left: 10px;">
		<div class="panel panel-default">
			<div class="panel-heading">Credito residuo</div>
			<div id="spec1" class="panel-body">
			{!! SmsController::printcredit() !!}
			</div>
		</div>

		</div>
    </div>
</div>
       

<?php

?>
@endsection
