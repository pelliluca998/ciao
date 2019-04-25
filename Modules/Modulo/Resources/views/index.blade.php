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
        <h1><i class='fas fa-calendar-alt'></i> Moduli iscrizione</h1>
        <p class="lead">Elenco dei moduli da compilare che puoi inserire dentro ai tuoi eventi</p>
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

          <table class="table table-bordered" id="moduloTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome modulo</th>
                <th>File</th>
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
      url: "{{route('modulo.index')}}"
    },
    display: "lightbox",
    table: "#moduloTable",
    fields: [
      {label: "Nome modulo", name: "label"},
      {label: "id_oratorio", name: "id_oratorio", type: "hidden", default: "{{ Session::get('session_oratorio') }}"},
      {label: "File", name: "path_file", type:"upload", display: function(val){
        return val;
      }
    }
  ]
});

$('#moduloTable').on('click', 'tbody td:not(:last-child)', function (e) {
  if("{{ !Auth::user()->can('edit-modulo') }}") return;
  editor.inline(this, {
    onBlur: 'submit',
    submit: 'allIfChanged'
  });
});

// Edit record
$('#moduloTable').on('click', 'button#editor_edit', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-modulo') }}") return;
  editor.edit( $(this).closest('tr'), {
    title: 'Modifica',
    buttons: 'Aggiorna'
  });
} );

// Delete a record
$('#moduloTable').on('click', 'button#editor_remove', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-modulo') }}") return;
  editor.remove( $(this).closest('tr'), {
    title: 'Cancella record',
    message: 'Sei sicuro di voler eliminare il modulo selezionato?',
    buttons: 'Cancella modulo'
  } );
} );

var buttons = [];
//Permesso di aggiungere gli utenti
if("{{ Auth::user()->can('edit-modulo') }}"){
  buttons.unshift(
    {
      extend: 'create',
      editor: editor,
      text: '<i class="fas fa-plus"></i> Aggiungi Modulo',
      className: 'btn btn-sm btn-primary',
      formButtons: [
        {
          label: 'Annulla',
          fn: function () { this.close(); }
        },
        '<i class="fas fa-save"></i> Salva modulo'
      ],
      formTitle: "Aggiungi modulo",
      formMessage: 'Inserisci i dati richiesti per il nuovo modulo e clicca su "Salva"'
    }
  );
}

$('#moduloTable').DataTable({
  responsive: true,
  processing: true,
  serverSide: true,
  dom: 'Bfrtip',
  language: { "url": "{{ asset('Italian.json') }}" },
  ajax: "{!! route('modulo.data') !!}",
  columns: [
    { data: 'id', visible: false},
    { data: 'label'},
    { data: 'path_file', render: function ( data, type, row ) {
      if (type === 'display') {
        return row['path_file'];
      }
      return data;
    }
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
