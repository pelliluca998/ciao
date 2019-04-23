<?php
?>

@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-ring'></i> Oratori</h1>
        <p class="lead">Aggiungi o modifica gli oratori della tua diocesi</p>
        <hr>
      </div>
    </div>
  </div>

	@if(Session::has('flash_message'))
	<div class="alert alert-success">
		{{ Session::get('flash_message') }}
	</div>
	@endif

	<div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col-lg">
      <div class="card">
        <div class="card-body">
					<table class="table table-bordered" id="oratoriTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>Email</th>
								<th></th>
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
var editor;
$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('oratori.index')}}"
    },
    display: "lightbox",
    table: "#oratoriTable",
    fields: [
      {label: "Nome oratorio", name: "nome"},
			{label: "Email", name: "email"},
    ]
  });

  $('#oratoriTable').on('click', 'tbody td:not(:last-child)', function (e) {
    editor.inline(this, {
      onBlur: 'submit',
      submit: 'allIfChanged'
    });
  });

  // Delete a record
  $('#oratoriTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'oratorio selezionato?',
      buttons: 'Cancella oratorio'
    } );
  } );

  var buttons = [];
    buttons.unshift(
			{
	      text: '<i class="fas fa-plus"></i> Aggiungi nuovo oratorio',
	      className: 'btn btn-sm btn-primary',
	      action: function ( e, dt, button, config ){
	        window.location = "{{ route('oratori.create') }}";
	      }
	    }
    );

  $('#oratoriTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    dom: 'Bfrtip',
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('oratori.data') !!}",
    columns: [
      { data: 'id', visible: false},
      { data: 'nome'},
			{ data: 'email'},
      { data: 'action', orderable: false, searchable: false}
    ],
    buttons: {
      dom: {
        button: {
          tag: 'button',
          className: ''
        }
      },
      buttons: buttons
    }
  });

});
</script>
@endpush
