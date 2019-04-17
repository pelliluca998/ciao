<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\User\Entities\Group;
use Modules\Oratorio\Entities\Type;
use App\Permission;

?>

@extends('layouts.app')

@section('content')
<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class="fas fa-bars"></i> Elenchi a scelta</h1>
        <p class="lead">Elenchi con opzioni personalizzate</p>
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
    <div class="col">
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered" id="typeTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th style="width: 20%;">Operazioni</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card">
        <div class="card-body" id="opzioni">
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var option = {};
var editor;
var table;

function load_opzioni(id_type){
  $("#opzioni").load("{!! url('admin/type/"+id_type+"/opzioni') !!}");
}


$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('type.index')}}"
    },
    display: "lightbox",
    table: "#typeTable",
    fields: [
      {label: "Nome", name: "label"},
      {label: "Descrizione", name: "description"},
      {label: "Id oratorio", name: "id_oratorio", type: "hidden", default: "{{ Session::get('session_oratorio') }}"},
    ]
  });


  $('#typeTable').on('click', 'tbody td:not(:last-child)', function (e) {
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor.inline(this, {
      onBlur: 'submit'
    });
  });

  // Edit record
  $('#typeTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#typeTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'elenco selezionato?',
      buttons: 'Cancella elenco'
    });
  });

var buttons = [];

//Permesso di aggiungere nuovo elenco
if("{{ Auth::user()->can('edit-select') }}"){
  buttons.unshift(
    {
      extend: 'create',
      editor: editor,
      text: '<i class="fas fa-plus"></i> Nuovo elenco',
      className: 'btn btn-sm btn-primary',
      formButtons: [
        {
          label: 'Annulla',
          fn: function () { this.close(); }
        },
        '<i class="fas fa-save"></i> Salva elenco'
      ],
      formTitle: "Nuovo elenco",
      formMessage: 'Inserisci i dati richiesti per il nuovo elenco e clicca su "Salva"'
    },
  );
}

  table = $('#typeTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('type.data') !!}",
    dom: 'Bfrtip',
    buttons: {
      dom: {
        button: {
          tag: 'button',
          className: ''
        }
      },
      buttons: buttons
    },

    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'label' },
      { data: 'description' },
      { data: 'action', orderable: false, searchable: false}
    ],
  });


});

</script>
@endpush
