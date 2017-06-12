<?php
use App\Event;
use App\Role;
use App\Permission;
use App\Email;
use App\User;
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
		<div class="panel-heading">Archivio Email inviate</div>
		<div class="panel-body">
			<?php
			$query = Email::select()
					->where('id_user', Auth::user()->id)
					->orderBy('created_at', 'desc');
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('email_report')
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
                		->setName('created_at')
					->setLabel('Data')
					->addFilter(
				    		(new FilterConfig)
				        		->setName('created_at')
							->setOperator(FilterConfig::OPERATOR_LIKE)
					)				
					# sorting buttons will be added to header, DB query will be modified
					->setSortable(true),
            		(new FieldConfig)
                		->setName('object')
					->setLabel('Oggetto')
					->addFilter(
				    		(new FilterConfig)
				        		->setName('object')
							->setOperator(FilterConfig::OPERATOR_LIKE)
					)				
					# sorting buttons will be added to header, DB query will be modified
					->setSortable(true),
				(new FieldConfig)
                		->setName('message')
					->setLabel('Messaggio')
					->addFilter(
				    		(new FilterConfig)
				        		->setName('message')
							->setOperator(FilterConfig::OPERATOR_LIKE)
					)				
					# sorting buttons will be added to header, DB query will be modified
					->setSortable(true),
				(new FieldConfig)
                		->setName('sent_to')
					->setLabel('Destinatario')
					->setCallback(function ($val, ObjectDataRow $row) {
							$dest = $row->getSrc();
							$users = json_decode($dest->sent_to);
							$us = User::whereIn('id', $users)->get();
							$string_users = "";
							foreach($us as $u){
								$string_users.= $u->name." ".$u->cognome.",";
							}
							
							return $string_users;
					}
					
					
					),
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
        </div>
    </div>
</div>

<?php

?>
@endsection
