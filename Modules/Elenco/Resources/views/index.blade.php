<?php
use Modules\Elenco\Entities\Elenco;
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
		<h1>Elenchi</h1>
		<p class="lead">Gli elenchi sono una lista di utenti, a cui puoi far corrispondere una o più casella. Sono utili ad esempio per la raccolta delle presenze, per gli elenchi dei pullman, ...</p>
		<hr>
	</div>
    <div class="row">
        <div class="">
		<div class="panel panel-default">
		<div class="panel-heading">Elenchi</div>
		<div class="panel-body">

            <!-- Modal2 -->
			<div class="modal fade" id="eventOp" tabindex="-1" role="dialog" aria-labelledby="EventOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni Evento</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica informazioni",
                             "desc" => "",
                            "url" => "elenco.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                            ["label" => "Riempimento rapido",
                             "desc" => "",
                            "url" => "elenco.show_riempi",
                            "class" => "btn-primary",
                             "icon" => ""],
                             ["label" => "Apri elenco",
                             "desc" => "",
                            "url" => "elenco.show",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Stampa elenco",
                             "desc" => "",
                            "url" => "elenco.print",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Elimina elenco",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "elenco.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'elenco <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_elenco', '0', ['id' => 'id_event']) !!}
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

		<a href='elenco/create' class="btn btn-primary">Aggiungi nuovo elenco</a>
			<?php			
			$query = (new Elenco)
			    ->newQuery()
			    ->where('id_event', '=', Session::get('work_event'));

			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('elenco_report')
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
                		->setName('nome')
					->setLabel('Nome')
					->addFilter(
				    		(new FilterConfig)
				        	->setName('nome')
				        	->setOperator(FilterConfig::OPERATOR_LIKE)
					)				
					# sorting buttons will be added to header, DB query will be modified
					->setSortable(true),				
				(new FieldConfig)
                		->setName('edit')
					->setLabel('')
					->setSortable(false)
					->setCallback(function ($val, ObjectDataRow $row) {
						$elenco = $row->getSrc();
                    		$icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#eventOp' data-name='".$elenco->nome."' data-eventid='".$elenco->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
						return $icon;
                        	})
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

<script>
$(document).ready(function(){
	$('#eventOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var eventid = button.data('eventid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_event']").val(eventid);
	});
});
</script>
@endsection
