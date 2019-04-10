<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\Attributo\Entities\Attributo;
use Modules\Oratorio\Entities\Type;
use App\Permission;

?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class="fas fa-paperclip"></i> Informazioni aggiuntive utenti</h1>
        <p class="lead">Elenco delle informazioni aggiuntive che puoi assegnare ai tuoi utenti</p>
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
    <div class="col-6">
      <div class="card">
        <div class="card-body">

          <table class="table table-bordered" id="attributoTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th>Tipo</th>
                <th>Nascosto</th>
                <th>Ordine</th>
                <th style="width: 20%;">Operazioni</th>
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
var option = {};
var lista_types = [];
var editor;
var table;

var types = '{!! Type::getTypes() !!}';
$.each(JSON.parse(types), function(i, e) {
  option.label = e;
  option.value = i;
  lista_types.push(option);
  option = {};
});


$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('attributo.index')}}"
    },
    display: "lightbox",
    table: "#attributoTable",
    fields: [
      {label: "Nome", name: "nome"},
      {label: "Descrizione", name: "note"},
      {label: "Tipo di attributo", name: "id_type", type:"select", options:lista_types},
      {label: "Nascosto", name: "hidden", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Ordine", name: "ordine", attr: {type: 'number', min: 0, step: 1}, def: 0},
      {label: "Id oratorio", name: "id_oratorio", type: "hidden", default: "{{ Session::get('session_oratorio') }}"},
    ]
  });


  $('#attributoTable').on('click', 'tbody td:not(:last-child)', function (e) {
    if("{{ !Auth::user()->can('edit-attributo') }}") return;
    editor.inline(this, {
      onBlur: 'submit'
    });
  });

  // Edit record
  $('#attributoTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-attributo') }}") return;
    editor.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#attributoTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-attributo') }}") return;
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'attributo selezionato?',
      buttons: 'Cancella attributo'
    });
  });

  var buttons = [];

  //Permesso per creare un nuovo attributo\
  if("{{ Auth::user()->can('edit-attributo') }}"){
    buttons.unshift(
      {
        extend: 'create',
        editor: editor,
        text: '<i class="fas fa-plus"></i> Nuovo attributo',
        className: 'btn btn-sm btn-primary',
        formButtons: [
          {
            label: 'Annulla',
            fn: function () { this.close(); }
          },
          '<i class="fas fa-save"></i> Salva attributo'
        ],
        formTitle: "Nuovo attributo",
        formMessage: 'Inserisci i dati richiesti per il nuovo attributo e clicca su "Salva"'
      }
    );
  }

  table = $('#attributoTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('attributo.data') !!}",
    dom: 'Bfrtip',
    buttons: {
      dom: {
        button: {
          tag: 'button',
          className: ''
        }
      },
      buttons: buttons,
    },

    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'nome', name: 'nome' },
      { data: 'note'},
      { data: 'type_label', editField: 'id_type'},
      {
        data:   "hidden",
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
      { data: 'ordine'},
      { data: 'action', orderable: false, searchable: false}
    ],


  });


});

</script>
@endpush
