<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\User\Entities\Group;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-users'></i> Gruppi</h1>
        <p class="lead">I gruppi del tuo oratorio</p>
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

          <table class="table table-bordered" id="groupTable" style="width: 100%">
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

    <div class="col-6">
      <div class="card">
        <div class="card-body" id="group-detail">
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

function load_componenti(id_gruppo){
  $("#group-detail").load("{!! url('admin/group/"+id_gruppo+"/componenti') !!}");
}


$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('group.index')}}"
    },
    display: "lightbox",
    table: "#groupTable",
    fields: [
      {label: "Nome", name: "nome"},
      {label: "Descrizione", name: "descrizione"},
      {label: "Id oratorio", name: "id_oratorio", type: "hidden", default: "{{ Session::get('session_oratorio') }}"},
    ]
  });


  $('#groupTable').on('click', 'tbody td:not(:last-child)', function (e) {
    if("{{ !Auth::user()->can('edit-gruppo') }}"){
      return;
    }
    editor.inline(this, {
      onBlur: 'submit'
    });
  });

  // Edit record
  $('#groupTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-gruppo') }}"){
      return;
    }
    editor.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#groupTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-gruppo') }}"){
      return;
    }
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare il gruppo selezionato?',
      buttons: 'Cancella gruppo'
    });
  });

  var buttons = [
    {
      text: '<i class="fas fa-sms"></i> Invia Sms',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('sms');
      }
    },
    {
      text: '<i class="fab fa-telegram-plane"></i> Invia Telegram',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('telegram');
      }
    },
  ];

  if("{{ Auth::user()->can('edit-gruppo') }}"){
    buttons.unshift(
      {
        extend: 'create',
        editor: editor,
        text: '<i class="fas fa-plus"></i> Nuovo gruppo',
        className: 'btn btn-sm btn-primary',
        formButtons: [
          {
            label: 'Annulla',
            fn: function () { this.close(); }
          },
          '<i class="fas fa-save"></i> Salva gruppo'
        ],
        formTitle: "Nuovo gruppo",
        formMessage: 'Inserisci i dati richiesti per il nuovo gruppo e clicca su "Salva"'
      },
    );
  }

  //permesso di inviare email
  if("{{ Auth::user()->can('send-email') && Module::find('email') != null && Module::find('email')->enabled() }}"){
    buttons.push(
      {
        text: '<i class="fas fa-envelope"></i> Invia Email',
        className: 'btn btn-sm btn-primary',
        action: function ( e, dt, button, config ){
          invia_utenti_selezionati('email');
        }
      }
    );
  }

  table = $('#groupTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('group.data') !!}",
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
      { data: 'nome', name: 'nome' },
      { data: 'descrizione', name: 'descrizione' },
      { data: 'action', orderable: false, searchable: false}
    ],
    select: {
      style:    'os',
      selector: 'td:last-child'
    },


  });


});

function invia_utenti_selezionati(action){
  var selected_rows = [];
  //costruisco l'array con tutti gli id utente selezionati
  $.each(table.rows('.selected').nodes(), function(i, item) {
    selected_rows.push(item.id);
  });
  if(selected_rows.length == 0){
    alert("Devi selezionare almeno un'utente!");
    return;
  }

  //invio in POST l'array e faccio il redirect alla pagina corretta in  base all'azione selezionata

  $.ajax({
    type: "POST",
    url: "{{ route('user.action') }}",
    data: {
      check_user: JSON.stringify(selected_rows),
      action: action,
      _token : "{{csrf_token()}}",
    },
    success: function(data){
      window.location = data;
    },
    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
  });
}

</script>
@endpush
