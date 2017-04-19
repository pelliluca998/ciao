<?php
use App\User;
use App\Role;
use App\Week;
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
<div class="container" style="">
	<div class="row">
		<h1>Settimane</h1>
		<p>Qui trovi le settimane in cui il tuo evento può essere suddiviso.</p>
		<hr>
	</div>
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row" style="">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default" style="">
		<div class="panel-heading">Settimane nell'evento selezionato</div>
		<div class="panel-body">
            
            <!-- Modal2 -->
			<div class="modal fade" id="weekOp" tabindex="-1" role="dialog" aria-labelledby="WeekOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni Settimana</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica informazioni",
                            "desc" => "",
                            "url" => "week.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                            ["label" => "Elimina settimana",
                            "desc" => "L'operazione è irreversibile!",
                            "url" => "week.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'evento <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_week', '0', ['id' => 'id_week']) !!}
                                {!! Form::submit($button['label'], ['class' => 'btn '.$button['class']]) !!}
                                {{$button['desc']}}
                                {!! Form::close() !!}
                                </div>
                            @endforeach

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>

						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>

            
            

		<a href='{!! route('week.create') !!}' class="btn btn-primary">Aggiungi nuova settimana</a>
			<?php
			$query = (new Week)
			    ->newQuery()
			    ->where('id_event', '=', Session::get('work_event'));
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('weeks')
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
                		->setName('from_date')
				# will be displayed in table header
				->setLabel('Data inizio')
				# That's all what you need for filtering. 
				# It will create controls, process input 
				# and filter results (in case of EloquentDataProvider -- modify SQL query)
				->addFilter(
				    (new FilterConfig)
				        ->setName('from_date')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('to_date')
				->setLabel('Data fine')
				->addFilter(
				    (new FilterConfig)
				        ->setName('to_date')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),

                (new FieldConfig)
                ->setName('edit')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$week = $row->getSrc();
                    $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#weekOp' data-name='".$week->from_date." - ".$week->to_date."' data-weekid='".$week->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
					return $icon;
                        	}),
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
						   	'remember_token',
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

<script>
$(document).ready(function(){
	$('#weekOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var weekid = button.data('weekid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_week']").val(weekid);
	});
});
</script>
@endsection
