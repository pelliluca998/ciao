<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\Event\Entities\Week;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<h1><i class="fas fa-sun" aria-hidden="true"></i> Settimane</h1>
		<p class="lead">Elenco delle settimane di cui è composto il tuo evento. Utile ad esempio per il CREGREST</p>
		<hr>
	</div>
	<div class="row">
		<div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
			<div class="panel panel-default">
				<div class="panel-body">
					@if(Session::has('flash_message'))
					<div class="alert alert-success">
						{{ Session::get('flash_message') }}
					</div>
					@endif

					<a href="{{ route('week.create')}}" class="btn btn-s btn-primary"><i class="fas fa-plus"></i> Aggiungi settimana</a><br><br>

					<table class="table table-bordered" id="weekTable" style="width: 100%">
						<thead>
							<tr>
								<th>Id</th>
								<th>Data di inizio</th>
								<th>Data di fine</th>
								<th>Operazioni</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


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

@endsection

@push('scripts')
<script>
function del_confirmation(){
	$("[data-toggle=confirmation]").confirmation({
		rootSelector: '[data-toggle=confirmation]',
		title: 'Sicuro di eliminare la settimana selezionata?',
		btnOkLabel: 'Si, elimina!',
		btnOkIcon: 'fa fa-share',
		btnOkClass: 'btn-success',
		btnCancelLabel: 'Annulla',
		btnCancelIcon: 'fa fa-times',
		btnCancelClass: 'btn-danger'
	});
}
$(document).ready(function(){
	$('#weekTable').DataTable({
		responsive: true,
		processing: true,
		serverSide: true,
		dom: 'Bfrtip',
		buttons: [
			{
				extend: 'excel',
				text: 'Esporta in Excel'
			},
			{
				extend: 'pdf',
				text: 'Esporta in PDF'
			},
			{
				extend: 'print',
				text: 'Stampa'
			},
			{
				extend: 'colvis',
				text: 'Colonne visibili'
			},
		],
		language: { "url": "{{ asset('Italian.json') }}" },
		ajax: "{!! route('week.data') !!}",
		columns: [
			{ data: 'id', name: 'id', visible: false},
			{ data: 'from_date', name: 'week.from_date' },
			{ data: 'to_date', name: 'week.to_date' },
			{ data: 'action', orderable: false, searchable: false}
		]


	});

	$('#weekTable').on('page.dt', function() {
		setTimeout(del_confirmation(), 500);
	});

	$('#weekTable').on('order.dt', function() {
		setTimeout(del_confirmation(), 500);
	});
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
@endpush
