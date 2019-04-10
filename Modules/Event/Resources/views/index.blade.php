<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-calendar-alt'></i> Eventi</h1>
        <p class="lead">Elenco degli eventi che stai organizzando</p>
        <hr>
      </div>
    </div>
  </div>

  <div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col-10">
      <div class="card">
        <div class="card-body">

          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          <table class="table table-bordered" id="eventsTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome evento</th>
                <th>Anno</th>
                <th>Descrizione</th>
                <th>Attivo</th>
                <th>Iscrizioni<br>multiple</th>
                <th style="width: 20%"></th>
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
      url: "{{route('events.index')}}"
    },
    display: "lightbox",
    table: "#eventsTable",
    fields: [
      {label: "Nome evento", name: "nome"},
      {label: "Anno", name: "anno", attr: {type: 'number', min: 2000, step: 1}, def: 2000},
      {label: "Descrizione", name: "descrizione", type:"tinymce", opts: {height: 400, width: '100%'}},
      {label: "Attivo", name: "active", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Iscrizioni multiple", name: "more_subscriptions", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
    ]
  });

  $('#eventsTable').on('click', 'tbody td:not(:last-child)', function (e) {
    if("{{ !Auth::user()->can('edit-event') }}") return;
    editor.inline(this, {
      onBlur: 'submit',
      submit: 'allIfChanged'
    });
  });

  // Edit record
  $('#eventsTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-event') }}") return;
    editor.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#eventsTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-event') }}") return;
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'evento selezionato?',
      buttons: 'Cancella evento'
    } );
  } );

  var buttons = [];
  if("{{ Auth::user()->can('edit-event') }}"){
    buttons.unshift(
      {
        text: '<i class="fas fa-plus"></i> Crea nuovo evento',
        className: 'btn btn-sm btn-primary',
        action: function ( e, dt, button, config ){
          window.location = "{!! route('events.create') !!}";
        }
      }
    );
  }

  $('#eventsTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    dom: 'Bfrtip',
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('events.data') !!}",
    columns: [
      { data: 'id', visible: false},
      { data: 'nome'},
      { data: 'anno' },
      { data: 'descrizione_label', editField: 'descrizione'},
      {
        data:   "active",
        render: function ( data, type, row ) {
          if (type === 'display') {
            if(data == 1){
              return "<i class='far fa-check-circle fa-2x'></i>";
            }else{
              return "<i class='far fa-circle fa-2x'></i>";
            }
          }
          return data;
        },
        className: "dt-body-center"
      },
      {
        data:   "more_subscriptions",
        render: function ( data, type, row ) {
          if (type === 'display') {
            if(data == 1){
              return "<i class='far fa-check-circle fa-2x'></i>";
            }else{
              return "<i class='far fa-circle fa-2x'></i>";
            }
          }
          return data;
        },
        className: "dt-body-center"
      },
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
