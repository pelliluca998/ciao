<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\Attributo\Entities\Attributo;
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
		<div class="panel-heading">Attributi</div>
		<div class="panel-body">
            
            <!-- Modal2 -->
			<div class="modal fade" id="attributoOp" tabindex="-1" role="dialog" aria-labelledby="AttributosOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni Attributo</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica informazioni",
                             "desc" => "",
                            "url" => "attributo.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                            ["label" => "Elimina Attributo",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "attributo.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'attributo <b><span id="name"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_attributo', '0', ['id' => 'id_attributo']) !!}
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


		<a href='{!! route('attributo.create') !!}' class="btn btn-primary">Aggiungi nuovo attributo</a>	
			<?php			
			$query = (new Attributo)
			    ->newQuery()
			    ->select('attributos.id', 'attributos.nome', 'types.label as type', 'attributos.hidden', 'attributos.note', 'attributos.ordine', 'attributos.id_type as id_type')
			    ->leftJoin('types', 'types.id', '=', 'attributos.id_type')
			    ->where('attributos.id_oratorio', '=', Session::get('session_oratorio'));
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('attributos_report')
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
				    ->setLabel('Nome Attributo')
                    ->addFilter(
				        (new FilterConfig)
				            ->setName('label')
				            ->setOperator(FilterConfig::OPERATOR_LIKE)
				        )
				    ->setSortable(true),
                (new FieldConfig)
                	->setName('note')
				    ->setLabel('Note')
				    ->addFilter(
				        (new FilterConfig)
				            ->setName('note')
				            ->setOperator(FilterConfig::OPERATOR_LIKE)
				        )				
				    # sorting buttons will be added to header, DB query will be modified
				    ->setSortable(true),
                (new FieldConfig)
                	->setName('ordine')
				    ->setLabel('Ordine')
				    ->addFilter(
				        (new FilterConfig)
				            ->setName('ordine')
				            ->setOperator(FilterConfig::OPERATOR_LIKE)
				        )				
				    # sorting buttons will be added to header, DB query will be modified
				    ->setSortable(true),
				(new FieldConfig)
                	->setName('type')
				    ->setLabel('Tipo')
				    ->setCallback(function ($val, ObjectDataRow $row) {
						$att = $row->getSrc();
						if($att->id_type>0){
							return $att->type;
						}else{
							switch($att->id_type){
								case -1:
									return "Testo";
									break;
								case -2:
									return "Checkbox";
									break;
								case -3:
									return "Numero";
									break;
								case -4:
									return "Gruppo";
									break;
							}
						}
					})
				    ->setSortable(true),
                (new FieldConfig)
                		->setName('hidden')
				    ->setLabel('Nascosto')
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
					    $attributo = $row->getSrc();
                        $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#attributoOp' data-name='".$attributo->nome."' data-attributoid='".$attributo->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
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
	$('#attributoOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var attributoid = button.data('attributoid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_attributo']").val(attributoid);
	});
});
</script>
@endsection
