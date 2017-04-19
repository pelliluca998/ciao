<?php
use App\User;
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
    <div class="row">
        <div class="">
		<div class="panel panel-default">
		<div class="panel-heading">Anagrafica Utenti</div>
		<div class="panel-body">


            <!-- Modal2 -->
			<div class="modal fade" id="userOp" tabindex="-1" role="dialog" aria-labelledby="UserOperation">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Operazioni utente</h4>
						</div>
                        <?php
                        $buttons = array(
                            ["label" => "Modifica informazioni",
                             "desc" => "",
                            "url" => "user.edit",
                            "class" => "btn-primary",
                            "icon" => ""],
                             ["label" => "Informazioni aggiuntive",
                             "desc" => "",
                            "url" => "attributouser.show",
                            "class" => "btn-primary",
                             "icon" => ""],                             
                            ["label" => "Stampa scheda",
                             "desc" => "",
                            "url" => "user.printprofile",
                            "class" => "btn-primary",
                             "icon" => ""],
                             ["label" => "Iscrivi ad un evento",
                             "desc" => "",
                            "url" => "subscription.selectevent",
                            "class" => "btn-primary",
                             "icon" => ""],
                            ["label" => "Elimina utente",
                             "desc" => "L'operazione è irreversibile!",
                            "url" => "user.destroy",
                            "class" => "btn-danger",
                            "icon" => ""]
                        );
                        ?>

						<div class="modal-body">

                            Operazioni disponibili per l'utente <b><span id="username"></span></b>:
                            @foreach ($buttons as $button)
                                <div style="margin: 5px;">
                                {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
                                {!! Form::hidden('id_user', 'id_user', ['id' => 'id_user']) !!}
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
			//array con i pulsanti in cima alla tabella
			$components = [
						(new HtmlTag)
                      			->setTagName('button')
                      			->setAttributes([
                            			'type' => 'submit',
										'name' => 'new_user',
										'value' => 'new_user',
                            			# Some bootstrap classes
                            			'class' => 'btn btn-primary'
                     			 	])
                      			->setContent("<i class='fa fa-plus'> Aggiungi Utente</i>"),
						(new HtmlTag)
                      			->setTagName('button')
                      			->setAttributes([
                            			'type' => 'submit',
										'name' => 'report',
										'value' => 'report',
                            			# Some bootstrap classes
                            			'class' => 'btn btn-primary'
                     			 	])
                      			->setContent(" <i class='fa fa-file-text'>Report</i> "),
										
					(new HtmlTag)
                      			->setTagName('button')
                      			->setAttributes([
                            			'type' => 'submit',
										'name' => 'group',
										'value' => 'group',
                            			# Some bootstrap classes
                            			'class' => 'btn btn-primary'
                     			 	])
                      			->setContent("<i class='fa fa-users'> Aggiungi ad un gruppo</i>")
					
					];
			//Se esiste il modulo sms, email e telegram, aggiungo i pulsanti
			if(Module::find('sms')!=null){
				$button = (new HtmlTag)
						->setTagName('button')
						->setAttributes([
							'type' => 'submit',
							'name' => 'sms',
							'value' => 'sms',
							# Some bootstrap classes
							'class' => 'btn btn-primary'
						])
						->setContent("<i class='fa fa-comment'> Invia SMS</i>");
				array_push($components, $button);
			}
			if(Module::find('email')!=null){
				$button = (new HtmlTag)
						->setTagName('button')
						->setAttributes([
							'type' => 'submit',
							'name' => 'email',
							'value' => 'email',
							# Some bootstrap classes
							'class' => 'btn btn-primary'
						])
						->setContent("<i class='fa fa-envelope'> Invia Email</i>");
				array_push($components, $button);
			}
			
			if(Module::find('telegram')!=null){
				$button = (new HtmlTag)
						->setTagName('button')
						->setAttributes([
							'type' => 'submit',
							'name' => 'telegram',
							'value' => 'telegram',
							# Some bootstrap classes
							'class' => 'btn btn-primary'
						])
						->setContent("<i class='fa fa-telegram'> Telegram</i>");
				array_push($components, $button);
			}
			$query = Input::query(); //sono i parametri GET della tabella
			Session::put('query_param', $query);
			$query = (new User)
			    ->newQuery()
				->select('users.*')
				->leftJoin('user_oratorio', 'users.id', '=', 'user_oratorio.id_user')
			    ->where('user_oratorio.id_oratorio', '=', Session::get('session_oratorio'));

			$grid = new Grid(
    			(new GridConfig)
			# Grids name used as html id, caching key, filtering GET params prefix, etc
			# If not specified, unique value based on file name & line of code will be generated
			->setName('my_report')
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
                		->setName('photo')
						->setLabel('User Photo')
						->setSortable(false)
						->setCallback(function ($val, ObjectDataRow $row) {
							$user = $row->getSrc();
							if($val==''){
								if($user->sesso=="M"){
									return "<img src='".url("upload/boy.png")."'>";
								}else if($user->sesso=="F"){
									return "<img src='".url("upload/girl.png")."'>";
								}

							}else{
								return "<img src='".url(Storage::url('public/'.$user->photo))."' width=48px/>";
							}
						}),
            	(new FieldConfig)
                		->setName('name')
				# will be displayed in table header
				->setLabel('Nome')
				# That's all what you need for filtering. 
				# It will create controls, process input 
				# and filter results (in case of EloquentDataProvider -- modify SQL query)
				->addFilter(
				    (new FilterConfig)
				        ->setName('name')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('cognome')
				->setLabel('Cognome')
				->addFilter(
				    (new FilterConfig)
				        ->setName('cognome')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)				
				# sorting buttons will be added to header, DB query will be modified
				->setSortable(true),
				(new FieldConfig)
                		->setName('sesso')
				->setLabel('Sesso')
				->setSortable(true),
				(new FieldConfig)
                		->setName('nato_il')
				->setLabel('Data di Nascita')
				->setSortable(true),
				(new FieldConfig)
                		->setName('nato_a')
				->setLabel('Luogo di nascita')
				->setSortable(true),
				(new FieldConfig)
                		->setName('residente')
				->setLabel('Residenza')
				->setSortable(true),
				(new FieldConfig)
                		->setName('via')
				->setLabel('Indirizzo')
				->setSortable(true),
				(new FieldConfig)
                		->setName('email')
				->setLabel('Email')
				->addFilter(
				    (new FilterConfig)
				        ->setName('email')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)
				->setSortable(true),
				(new FieldConfig)
                		->setName('cell_number')
				->setLabel('Telefono')
				->addFilter(
				    (new FilterConfig)
				        ->setName('cell_number')
				        ->setOperator(FilterConfig::OPERATOR_LIKE)
				)
				->setSortable(true),
				(new FieldConfig)
                		->setName('username')
				->setLabel('Username')
				->setSortable(true),
				(new FieldConfig)
                		->setName('check')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$user = $row->getSrc();
					$icon = "<input name='check_user[]' id='check_users_".$user->id."' type='checkbox' value='".$user->id."'/>";
					return $icon;
                        	}),
				(new FieldConfig)
                ->setName('edit')
				->setLabel('')
				->setSortable(false)
				->setCallback(function ($val, ObjectDataRow $row) {
					$user = $row->getSrc();
                    $icon = "<button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#userOp' data-username='".$user->name." ".$user->cognome."' data-userid='".$user->id."'><i class='fa fa-pencil fa-2x' aria-hidden='true'></i> </button>";
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
												'name' => 'filter',
		                            			# Some bootstrap classes
		                            			'class' => 'btn btn-primary'
                               			 	])
                                			->setContent('Filtra')
					])
					# Components may have some placeholders for rendering children there.
					->setRenderSection(THead::SECTION_BEGIN),
					(new OneCellRow)
							->setComponents($components)->setRenderSection(THead::SECTION_BEGIN),

				]),
				# Renders table footer (table>tfoot)
				(new TFoot)

			])
			);

	echo $grid->render();
			?>

<script>
$(document).ready(function(){
	$('#userOp').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var username = button.data('username') // Extract info from data-* attributes
	var userid = button.data('userid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#username').text(username);
	modal.find("[id*='id_user']").val(userid);
	modal.find("[name*='id_user']").val(userid);
	});
});
</script>

@endsection
