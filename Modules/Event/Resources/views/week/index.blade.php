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
		<div class="col">
			<div class="card bg-transparent border-0">
				<h1><i class='fas fa-sun'></i> Settimane</h1>
				<p class="lead">Elenco delle settimane di cui è composto il tuo evento. Utile ad esempio per il CREGREST</p>
				<hr>
			</div>
		</div>
	</div>


	<div class="row justify-content-center" style="margin-top: 20px;">
		<div class="col-6">
			<div class="card">
				<div class="card-body">

					@if(Session::has('flash_message'))
					<div class="alert alert-success">
						{{ Session::get('flash_message') }}
					</div>
					@endif

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


@endsection

@push('scripts')
<script>
$(document).ready(function(){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': '{{csrf_token()}}'
		}
	});

	var editor = new $.fn.dataTable.Editor({
		ajax: {
			url: "{{route('week.index')}}"
		},
		display: "lightbox",
		table: "#weekTable",
		fields: [
			{label: "ID Evento", name: 'id_event', type: 'hidden', default: "{{ Session::get('work_event') }}" },
			{label: "Data di inizio", name: "from_date", type:"datetime", format:"DD/MM/YYYY"},
			{label: "Data di fine", name: "to_date", type:"datetime", format:"DD/MM/YYYY"},
		]
	});

	$('#weekTable').on('click', 'tbody td', function (e) {
		editor.inline(this, {
			onBlur: 'submit',
			submit: 'allIfChanged'
		});
	});

	// Edit record
	$('#weekTable').on('click', 'button#editor_edit', function (e) {
		e.preventDefault();
		editor.edit( $(this).closest('tr'), {
			title: 'Modifica',
			buttons: 'Aggiorna'
		});
	} );

	// Delete a record
	$('#weekTable').on('click', 'button#editor_remove', function (e) {
		e.preventDefault();

		editor.remove( $(this).closest('tr'), {
			title: 'Cancella record',
			message: 'Sei sicuro di voler eliminare la settimana selezionato?',
			buttons: 'Cancella settimana'
		} );
	} );

	$('#weekTable').DataTable({
		responsive: true,
		processing: true,
		serverSide: true,
		dom: 'Bfrtip',
		language: { "url": "{{ asset('Italian.json') }}" },
		ajax: "{!! route('week.data') !!}",
		columns: [
			{ data: 'id', name: 'id', visible: false},
			{ data: 'from_date', name: 'week.from_date' },
			{ data: 'to_date', name: 'week.to_date' },
			{ data: 'action', orderable: false, searchable: false}
		],
		buttons: {
			dom: {
				button: {
					tag: 'button',
					className: ''
				}
			},
			buttons: [
				{
					extend: 'create',
					editor: editor,
					text: '<i class="fas fa-plus"></i> Aggiungi Settimana',
					className: 'btn btn-sm btn-primary btn-block',
					formButtons: [
						{
							label: 'Annulla',
							fn: function () { this.close(); }
						},
						'<i class="fas fa-save"></i> Salva settimana'
					],
					formTitle: "Aggiungi settimana",
					formMessage: 'Inserisci i dati richiesti e clicca su "Salva"'
				},
			]
		}


	});
});
</script>
@endpush
