<?php
use App\User;
use App\Role;
use App\Group;
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
		<div class="panel panel-default" style="width: 50%; float: left; margin-right: 2%">
		<div class="panel-heading">Gruppi di utenti</div>
		<div class="panel-body">
            
            <!-- Modal2 -->
			<div class="modal fade" id="groupOp" tabindex="-1" role="dialog" aria-labelledby="GroupOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni gruppo</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica informazioni",
                             "desc" => "",
                            "url" => "group.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                             ["label" => "Stampa elenco componenti",
                             "desc" => "",
                            "url" => "group.report_composer",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Elimina gruppo",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "group.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per il gruppo <b><span id="username"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_group', '0', ['id' => 'id_group']) !!}
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
            
            

			<?php
			
			$query = (new Group)
			    ->newQuery()
			    ->where('id_oratorio', '=', Session::get('session_oratorio'));
			
			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('groupd_report')
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
				->setLabel('Nome gruppo')
				->addFilter(
				    (new FilterConfig)
				        ->setName('nome')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('descrizione')
				->setLabel('Descrizione')
				->addFilter(
				    (new FilterConfig)
				        ->setName('descrizione')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('check')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$user = $row->getSrc();
					$icon = "<input name='check_groups[]' type='checkbox' value='".$user->id."'/>";
					return $icon;
                        	}),
                        	(new FieldConfig)
                		->setName('Componenti')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$sub = $row->getSrc();
					$click = "$('#spec').load('groupusers/show/".$sub->id."')";
					$icon = "<i onclick=\"$click\" class='fa fa-users fa-2x' aria-hidden='true'></i>";
					return $icon;
                        	}),
                (new FieldConfig)
                ->setName('edit')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$group = $row->getSrc();
                    $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#groupOp' data-username='".$group->nome."' data-groupid='".$group->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
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
					->setRenderSection(THead::SECTION_BEGIN),
					(new OneCellRow)
							->setComponents([
                                (new HtmlTag)
                                			->setTagName('button')
                                			->setAttributes([
		                            			'type' => 'submit',
												'name' => 'new',
												'value' => 'new',
		                            			# Some bootstrap classes
		                            			'class' => 'btn btn-primary'
                               			 	])
                                			->setContent("<i class='fa fa-plus'> Aggiungi Gruppo</i> "),
								(new HtmlTag)
                                			->setTagName('button')
                                			->setAttributes([
		                            			'type' => 'submit',
												'name' => 'email',
												'value' => 'email',
		                            			# Some bootstrap classes
		                            			'class' => 'btn btn-primary'
                               			 	])
                                			->setContent("<i class='fa fa-envelope'> Invia Email</i>")

							])->setRenderSection(THead::SECTION_BEGIN),
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
            
            <div class="panel panel-default" style="width: 48%; float: left;">
		<div class="panel-heading">Componenti del gruppo</div>
		<div id="spec" class="panel-body">
		 
		</div>
        </div>
    </div>
</div>
       
        
<script>
$(document).ready(function(){
	$('#groupOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var username = button.data('username') // Extract info from data-* attributes
	var groupid = button.data('groupid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#username').text(username);
	//modal.find('#id_group').val(groupid);
    modal.find("[id*='id_group']").val(groupid);
	});
});
</script>
@endsection
