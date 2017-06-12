<?php
use App\Event;
use App\Role;
use App\User;
use App\Bilancio;
use App\Permission;
use App\EventSpec;
use App\EventSpecValues;
use App\Cassa;
use App\ModoPagamento;
use App\TipoPagamento;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\SelectFilterConfig;
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
		<div class="panel-heading">Contabilità</div>
		<div class="panel-body">

            <!-- Modal2 -->
			<div class="modal fade" id="bilancioOp" tabindex="-1" role="dialog" aria-labelledby="EventOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni voce di bilancio</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica voce",
                             "desc" => "",
                            "url" => "contabilita.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                             
                            ["label" => "Elimina voce",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "contabilita.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per la voce: <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_bilancio', '0', ['id' => 'id_bilancio']) !!}
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
			
			<div style="width: 40%; background-color: #FFFACD; border-radius: 10px; margin-left: 30%; padding: 10px;">
				<h3>Bilancio attuale</h3>
				<?php
				$bilancio = DB::table('bilancio')
					->select('tipo_cassa.label', DB::raw('SUM(importo) as totale'))
					->leftJoin('tipo_cassa', 'tipo_cassa.id', 'bilancio.id_cassa')
					->where('bilancio.id_event', Session::get('work_event'))
					->groupBy('bilancio.id_cassa')
					->get();
				?>
				<table class='testgrid' style='background-color: white;'>
				<thead>
					<tr>
						<th>Cassa</th>
						<th>Importo</th>
					</tr>
				</thead>
				@foreach($bilancio as $b)
					<tr>
						<td>{{$b->label}}</td>
						<td>						
						@if($b->totale>0)
							<p style="background-color: chartreuse; padding: 4px;">{{$b->totale}}€</p>
						@elseif($b->totale<0)
							<p style="background-color: coral; padding: 4px;">{{$b->totale}}€</p>
						@else
							{{$b->totale}}
						@endif
						</td>
					</tr>
				@endforeach
				</table>
			</div>

		<a href='contabilita/create' class="btn btn-primary">Aggiungi entrata/uscita</a> <a href='contabilita/report' class="btn btn-primary">Esporta Report</a>
			<?php			
			$query = (new Bilancio)
			    ->newQuery()
			    ->where('id_event', '=', Session::get('work_event'))
			    ->orderBy('id', 'ASC');

			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('bilancio_report')
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
                			->setName('id_tipopagamento')
						->setLabel('Tipologia pagamento')
						->addFilter(
							(new SelectFilterConfig)
								->setName('id_tipopagamento')
								->setOptions(TipoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id')->toArray())
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
							$b = $row->getSrc();
							$tipo = TipoPagamento::where('id', $b->id_tipopagamento)->get();
							if(count($tipo)>0){
								return $tipo[0]->label;
							}else{
								return "n.d.";
							}
						}),
					(new FieldConfig)
                			->setName('id_eventspecvalues')
						->setLabel('Specifica')
						->addFilter(
				    			(new FilterConfig)
				       			->setName('id_eventspecvalues')
				        			->setOperator(FilterConfig::OPERATOR_LIKE)
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
							$b = $row->getSrc();
							$spec = EventSpec::select('event_specs.id', 'event_specs.label')->
							leftJoin('event_spec_values', 'event_spec_values.id_eventspec', 'event_specs.id')
							->where('event_spec_values.id', $b->id_eventspecvalues)
							->get();
							if(count($spec)>0){
								return $spec[0]->label;
							}else{
								return $b->id_eventspecvalues;
							}
						}),
					(new FieldConfig)
                			->setName('descrizione')
						->setLabel('Descrizione')
						->addFilter(
				    			(new FilterConfig)
				       			->setName('descrizione')
				        			->setOperator(FilterConfig::OPERATOR_LIKE)
						)
						->setSortable(true),
					(new FieldConfig)
                			->setName('id_modalita')
						->setLabel('Mod. Pagamento')
						->addFilter(
							(new SelectFilterConfig)
								->setName('id_modalita')
								->setOptions(ModoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id')->toArray())
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
							$b = $row->getSrc();
							$modo = ModoPagamento::where('id', $b->id_modalita)->get();
							if(count($modo)>0){
								return $modo[0]->label;
							}else{
								return "n.d.";
							}
						}),
					(new FieldConfig)
                			->setName('id_cassa')
						->setLabel('Cassa')
						->addFilter(
							(new SelectFilterConfig)
								->setName('id_cassa')
								->setOptions(Cassa::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id')->toArray())
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
							$b = $row->getSrc();
							$cassa = Cassa::where('id', $b->id_cassa)->get();
							if(count($cassa)>0){
								return $cassa[0]->label;
							}else{
								return "n.d.";
							}
						}),
					(new FieldConfig)
                			->setName('importo')
						->setLabel('Importo')
						->addFilter(
				    			(new FilterConfig)
				       			->setName('importo')
				        			->setOperator(FilterConfig::OPERATOR_LIKE)
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
								$b = $row->getSrc();
								if($b->importo>0){
									return "<p style='background-color: chartreuse; padding: 4px;'>".$b->importo."€</p>";
								}elseif($b->importo<0){
									return "<p style='background-color: coral; padding: 4px;'>".$b->importo."€</p>";
								}else{
									return $b->importo;
								}
							}),
					(new FieldConfig)
                			->setName('id_user')
						->setLabel('Eseguito da')
						->addFilter(
				    			(new FilterConfig)
				       			->setName('id_user')
				        			->setOperator(FilterConfig::OPERATOR_LIKE)
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true)
						->setCallback(function ($val, ObjectDataRow $row) {
							$b = $row->getSrc();
							$user = User::where('id', $b->id_user)->get();
							if(count($user)>0){
								return $user[0]->cognome."<br>".$user[0]->name;
							}else{
								return "n.d.";
							}
						}),
					(new FieldConfig)
                			->setName('data')
						->setLabel('Data')
						->addFilter(
				    			(new FilterConfig)
				       			->setName('created_at')
				        			->setOperator(FilterConfig::OPERATOR_LIKE)
						)				
						# sorting buttons will be added to header, DB query will be modified
						->setSortable(true),
						
					(new FieldConfig)
               			->setName('edit')
						->setLabel('')
						->setSortable(false)
						->setCallback(function ($val, ObjectDataRow $row) {
							$bilancio = $row->getSrc();
                    			$icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#bilancioOp' data-name='".$bilancio->descrizione."' data-bilancioid='".$bilancio->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
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
	$('#bilancioOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var bilancioid = button.data('bilancioid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_bilancio']").val(bilancioid);
	});
});
</script>
@endsection
