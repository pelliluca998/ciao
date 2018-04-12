<?php
use Modules\Event\Entities\Event;
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
		<div class="panel-heading">Eventi</div>
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
                            "url" => "events.edit",
                            "class" => "btn-primary",
                            "toggle" => "",
                            "icon" => ""],
                             ["label" => "Lavora con questo evento",
                             "desc" => "",
                            "url" => "events.work",
                            "class" => "btn-primary",
                            "toggle" => "",
                             "icon" => ""],
                            ["label" => "Vedi iscrizioni",
                             "desc" => "",
                            "url" => "subscription.event",
                            "toggle" => "",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Specifiche",
                             "desc" => "",
                            "url" => "eventspecs.show",
                            "toggle" => "",
                            "class" => "btn-primary",
                             "icon" => ""],
                             ["label" => "Clona",
                              "desc" => "",
                             "url" => "events.clone",
                             "class" => "btn-primary",
                             "toggle" => "",
                             "icon" => ""],
                            ["label" => "Elimina evento",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "events.destroy",
                            "class" => "btn-danger",
                            "toggle" => "confirmation",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'evento <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_event', '0', ['id' => 'id_event']) !!}
                                {!! Form::submit($button['label'], ['class' => 'btn '.$button['class'], 'data-toggle' => $button['toggle']]) !!}
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

		<a href='events/create' class="btn btn-primary">Aggiungi nuovo evento</a>
			<?php
			$query = (new Event)
			    ->newQuery()
			    ->where('id_oratorio', '=', Session::get('session_oratorio'));

			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('events_report')
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
                		->setName('anno')
				->setLabel('Anno')
				->addFilter(
				    (new FilterConfig)
				        ->setName('anno')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('descrizione')
				->setLabel('Descrizione')
				->setSortable(true),
				(new FieldConfig)
                		->setName('active')
				->setLabel('Attivo')
				->setSortable(true)
				->setCallback(function ($val, ObjectDataRow $row) {
					$sub = $row->getSrc();
					$icon = "<i class='fa ";
					if($val==1){
						$icon .= "fa-check-square-o";
					}else{
						$icon .= "fa-square-o";
					}
					$icon .= " fa-2x' aria-hidden='true'></i>";
					return $icon;
                        	}),
				(new FieldConfig)
                	->setName('edit')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$event = $row->getSrc();
                    $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#eventOp' data-name='".$event->nome."' data-eventid='".$event->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
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

	$('[data-toggle=confirmation]').confirmation({
		rootSelector: '[data-toggle=confirmation]',
		title: 'Sicuro di eliminare l\'evento selezionato?',
		btnOkLabel: 'Si, elimina!',
		btnOkIcon: 'glyphicon glyphicon-share-alt',
		btnOkClass: 'btn-success',
		btnCancelLabel: 'Annulla',
		btnCancelIcon: 'glyphicon glyphicon-ban-circle',
		btnCancelClass: 'btn-danger'
	});
});
</script>

@endsection
