<?php
use App\Subscription;
use App\Event;
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
		<div class="panel-heading">Iscrizioni all'evento</div>
		<div class="panel-body">
            
            <!-- Modal2 -->
			<div class="modal fade" id="subOp" tabindex="-1" role="dialog" aria-labelledby="SubscriptionOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni Iscrizione</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica iscrizione",
                             "desc" => "",
                            "url" => "subscription.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                             ["label" => "Stampa iscrizione",
                             "desc" => "",
                            "url" => "subscription.print",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Elimina iscrizione",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "subscription.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'iscrizione di  <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_sub', '0', ['id' => 'id_sub']) !!}
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



			<a href='{!! route('subscription.contact') !!}' class="btn btn-primary">Contatta tutti gli iscritti</a>
			<?php
			$query = Input::query(); //sono i parametri GET della tabella
			Session::put('query_param', $query);
			if(!isset($id_event) || $id_event==null){
				$id_event=Session::get('work_event');
			}
            $event = Event::findOrFail($id_event);
            if($event->stampa_anagrafica==1){
                //nel campo utente, stampo il nome dell'utente così come compare in anagrafica
                $query = (new Subscription)
                    ->newQuery()
                    ->select(DB::raw("concat(users.name, ' ', users.cognome) as name"),
                             'subscriptions.id', 'subscriptions.confirmed', 'subscriptions.type')
                    ->leftJoin('users', 'users.id', '=', 'subscriptions.id_user')
                    ->where('subscriptions.id_event', '=', $id_event);
            }else{
                //il campo 'utente' è in una delle specifiche dell'iscrizione
                $query = (new Subscription)
                    ->newQuery()
                    ->select(DB::raw("event_spec_values.valore as name"),
                             'subscriptions.id', 'subscriptions.confirmed', 'subscriptions.type')
                    ->leftJoin('event_spec_values', 'event_spec_values.id_subscription', '=', 'subscriptions.id')
                    ->where('subscriptions.id_event', '=', $id_event)
                    ->where('event_spec_values.id_eventspec', '=', $event->spec_iscrizione);
            }
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('subscriptions_report')
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
                    ->setName('name')
				    ->setLabel('Utente')
				    ->addFilter(
				        (new FilterConfig)
				            ->setName('name')
				            ->setOperator(FilterConfig::OPERATOR_LIKE))
				            # sorting buttons will be added to header, DB query will be modified
				            ->setSortable(true),
				(new FieldConfig)
                	->setName('confirmed')
				    ->setLabel('OK?')
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
                		->setName('type')
				->setLabel('Tipo')
				->setSortable(true),				

                (new FieldConfig)
                ->setName('edit')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$sub = $row->getSrc();
                    $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#subOp' data-name='".$sub->name."' data-subid='".$sub->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
					return $icon;
                        	}),
                        	(new FieldConfig)
                		->setName('Vedi specifiche')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$sub = $row->getSrc();
					$click = "load_spec_subscription(".$sub->id.")";
					//$click = "$('#spec').load('specsubscriptions/4')";
					//$click="$('#spec').html('ciao ciao')";
					$icon = "<i style= \"color:#3e93c3; cursor: pointer;\" onclick=\"$click\" class='fa fa-flag fa-2x' aria-hidden='true'></i>";
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
            
		<div class="panel-right">
			<div class="panel panel-default">
				<div class="panel-heading">Specifiche iscrizione</div>
				<div id="spec1" class="panel-body">
				 
				</div>
			</div>			
		</div>
    </div>
</div>
       
        
<script>
$(document).ready(function(){
	$('#subOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var subid = button.data('subid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_sub']").val(subid);
	});
});
</script>
@endsection
