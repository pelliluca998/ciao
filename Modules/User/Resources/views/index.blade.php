<?php
use Modules\User\Entities\User;
use Modules\Attributo\Entities\Attributo;
use App\Role;
use App\Permission;
use App\Provincia;
use App\Nazione;

$default_role = Role::where([['id_oratorio', Session::get('session_oratorio')], ['name', 'user']])->first()->id;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-user'></i> Anagrafica</h1>

        <p class="lead">Elenco degli utenti iscritti al tuo oratorio</p>
        <hr>
      </div>
    </div>
  </div>


  <div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col">
      <div class="card">
        <div class="card-body">

          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          <table class="table table-bordered" id="usersTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Foto</th>
                <th>Cognome</th>
                <th>Nome</th>
                <th>Cellulare</th>
                <th>Email</th>
                <th>Data di nascita</th>
                <th>Luogo di nascita</th>
                <th>C.F.</th>
                <th>Tessera sanitaria</th>
                <th style="width: 15%;">Operazioni</th>
              </tr>
            </thead>
          </table>

        </div>
      </div>
    </div>
  </div>
</div>

<!--  Modal per l'edit degli attributi-->
<div class="modal fade" id="modal_attributi" tabindex="-1" role="dialog" aria-labelledby="modal_attributi" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modifica attributi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="attributoTable" style="width: 100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Attributo</th>
              <th>Valore</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
        @if(Auth::user()->can('edit-attributo'))
        <button type="button" class="btn btn-primary" id="button_create_attribute" data-dismiss="modal">Aggiungi nuova informazioni</button>
        @endif
      </div>
    </div>
  </div>
</div>
<!--  End modal-->

@endsection

@push('scripts')
<script>
var option = {};
var scuole = [];
var lista_province = [];
var lista_attributi = [];
var lista_ruoli = [];
var lista_nazioni = [];
var editor;
var table;

var prov = '{!! Provincia::getLista() !!}';
$.each(JSON.parse(prov), function(i, e) {
  option.label = e.nome;
  option.value = e.id;
  lista_province.push(option);
  option = {};
});

var attr = '{!! Attributo::getLista() !!}';
$.each(JSON.parse(attr), function(i, e) {
  option.label = e.nome;
  option.value = e.id;
  lista_attributi.push(option);
  option = {};
});

var nazioni = '{!! Nazione::getLista() !!}';
$.each(JSON.parse(nazioni), function(i, e) {
  option.label = e.nome;
  option.value = e.id;
  lista_nazioni.push(option);
  option = {};
});

var ruoli = '{!! Role::getLista() !!}';
$.each(JSON.parse(ruoli), function(i, e) {
  option.label = e.nome;
  option.value = e.id;
  lista_ruoli.push(option);
  option = {};
});


function getListaComuni(id_provincia, field_name){
  var lista_comuni = [];
  $.ajax({
    type: 'GET',
    dataType: 'json',
    url: "{{route('comune.lista')}}",
    data: {
      id_provincia: id_provincia
    },
    success: function(response) {
      $.each(response, function(index, element) {
        option.label = element.nome;
        option.value = element.id;
        lista_comuni.push(option);
        option = {};
      });

      editor.field(field_name).update( lista_comuni );
    },
    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
    async: false
  });
}

$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('user.index')}}"
    },
    display: "lightbox",
    table: "#usersTable",
    fields: [
      {label: "Nome", name: "name"},
      {label: "Cognome", name: "cognome"},
      {label: 'Numero di cellulare', name: 'cell_number'},
      {label: 'Email', name: 'email'},
      {label: 'Sesso', name: 'sesso', type:"select", options:[{label: "Uomo", value: 'M'}, {label: "Donna", value: 'F'}]},
      {label: "Numero di tessera sanitaria", name: "tessera_sanitaria"},
      {label: "Codice fiscale", name: "cod_fiscale"},
      {label: "Foto profilo", name: "photo", type:"upload", display: function(val){
        return val;
      }
    },
    {label: "Luogo e data di nascita", name: "nascita_info", type: "title"},
    {label: "Data di nascita", name: "nato_il", type:"datetime", format:"DD/MM/YYYY"},
    {label: "Nazione di nascita", name: "id_nazione_nascita", type:"select", options:lista_nazioni, default: 118},
    {label: "Provincia di nascita", name: "id_provincia_nascita", type:"select", options:lista_province},
    {label: "Comune di nascita", name: "id_comune_nascita", type:"select", options:[]},
    {label: "Residenza", name: "residenza_info", type: "title"},
    {label: "Provincia di residenza", name: "id_provincia_residenza", type:"select", options:lista_province},
    {label: "Comune di residenza", name: "id_comune_residenza", type:"select", options:[]},
    {label: 'Indirizzo', name: 'via'},
    {label: "Dati sanitari", name: "sanitari_info", type: "title"},
    {label: "Patologie e terapie in corso", name: "patologie", type: "textarea"},
    {label: "Allergie e intolleranze", name: "allergie", type: "textarea"},
    {label: "Altro", name: "note", type: "textarea"},
    {label: "Privacy e consensi", name: "privacy_info", type: "title"},
    {label: "Consenso ad invio comunicazioni", name: "consenso_affiliazione", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
    {label: "Ruolo", name: "ruolo_info", type: "title"},
    {label: "Ruolo e permessi", name: "role_id", type:"select", options:lista_ruoli, default: "{{ $default_role }}"},
  ]
});

$(editor.field('id_provincia_nascita').input() ).on('change', function (e, d) {
  if ( !d || !d.editor ) {
    getListaComuni(editor.field('id_provincia_nascita').val(), 'id_comune_nascita');
  }
});

$(editor.field('id_nazione_nascita').input() ).on('change', function (e, d) {
  if ( !d || !d.editor ) {
    if(editor.field('id_nazione_nascita').val() == 118){
      editor.field('id_provincia_nascita').show();
      editor.field('id_comune_nascita').show();
    }else{
      editor.field('id_provincia_nascita').hide();
      editor.field('id_comune_nascita').hide();
    }
  }
});

$(editor.field('id_provincia_residenza').input() ).on('change', function (e, d) {
  if ( !d || !d.editor ) {
    getListaComuni(editor.field('id_provincia_residenza').val(), 'id_comune_residenza');
  }
});

$('#usersTable').on('click', 'tbody td:not(:first-child):not(:last-child)', function (e) {
  //Abilito edito solo se l'utente ne ha il permesso
  if("{{ !Auth::user()->can('edit-users') }}"){
    return;
  }
  editor.inline(this, {
    onBlur: 'submit',
    submit: 'all'
  });
});

// Edit record
$('#usersTable').on('click', 'button#editor_edit', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-users') }}"){
    return;
  }
  editor.edit( $(this).closest('tr'), {
    title: 'Modifica',
    buttons: 'Aggiorna'
  });
} );

// Delete a record
$('#usersTable').on('click', 'button#editor_remove', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-users') }}"){
    return;
  }
  editor.remove( $(this).closest('tr'), {
    title: 'Cancella record',
    message: 'Sei sicuro di voler eliminare l\'utente selezionato?',
    buttons: 'Cancella utente'
  });
});

editor.on( 'initEdit', function ( e, node, data, items, type ) {
  getListaComuni(data['id_provincia_nascita'], 'id_comune_nascita');
  getListaComuni(data['id_provincia_residenza'], 'id_comune_residenza');
  if(data['id_nazione_nascita'] == 118){
    editor.field('id_provincia_nascita').show();
    editor.field('id_comune_nascita').show();
  }else{
    editor.field('id_provincia_nascita').hide();
    editor.field('id_comune_nascita').hide();
  }
} );

var buttons = [
  {
    text: '<i class="fab fa-telegram-plane"></i> Invia Telegram',
    className: 'btn btn-sm btn-primary',
    action: function ( e, dt, button, config ){
      invia_utenti_selezionati('telegram');
    }
  },
];

//Permesso di aggiungere gli utenti
if("{{ Auth::user()->can('edit-users') }}"){
  buttons.unshift(
    {
      extend: 'create',
      editor: editor,
      text: '<i class="fas fa-plus"></i> Aggiungi utente',
      className: 'btn btn-sm btn-primary',
      formButtons: [
        {
          label: 'Annulla',
          fn: function () { this.close(); }
        },
        '<i class="fas fa-save"></i> Salva utente'
      ],
      formTitle: "Aggiungi utente",
      formMessage: 'Inserisci i dati richiesti per il nuovo utente e clicca su "Salva"'
    }
  );
}

//permesso di aggiungere ad un gruppo
if("{{ Auth::user()->can('edit-gruppo') }}"){
  buttons.push(
    {
      text: '<i class="fas fa-plus"></i> Aggiungi ad un gruppo',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('group');
      }
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

//Permesso di inviare SMS
if("{{ Auth::user()->can('send-sms') }}"){
  buttons.push(
    {
      text: '<i class="fas fa-sms"></i> Invia Sms',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('sms');
      }
    }
  );
}

//permesso di stampare report
if("{{ Auth::user()->can('generate-report') }}"){
  buttons.push(
    {
      text: '<i class="fas fa-file-alt"></i> Genera report',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        window.location = "{{ route('report.user') }}";
      }
    }
  );
}

table = $('#usersTable').DataTable({
  responsive: true,
  processing: true,
  serverSide: true,
  language: { "url": "{{ asset('Italian.json') }}" },
  ajax: "{!! route('user.data') !!}",
  dom: 'Blfrtip',
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
  {data: 'photo', className: "dt-body-center", render: function ( data, type, row ) {
    if (type === 'display') {
      return row['path_photo'];
    }
    return data;
  }
},
{ data: 'cognome', name: 'cognome' },
{ data: 'name', name: 'name' },
{ data: 'cell_number', name: 'cell_number' },
{ data: 'email_label', editField: 'email' },
{ data: 'nato_il', name: 'nato_il' },
{ data: 'comune_nascita_label', name: 'comune_nascita_label', editField: 'id_comune_nascita'},
{ data: 'cod_fiscale' },
{ data: 'tessera_sanitaria' },
{ data: 'action', orderable: false, searchable: false, responsivePriority: 1}
],
select: {
  style:    'os',
  selector: 'td:first-child'
},
});


// Editor degli attributi user
editor_attributi = new $.fn.dataTable.Editor({
  ajax: {
    url: "{{route('attributouser.index')}}"
  },
  display: "lightbox",
  table: "#attributoTable",
  fields: [
    {label: "Attributo", name: "id_attributo", type:"select", options:lista_attributi},
    {label: "Valore", name: "valore"},
    {label: "id_user", name: "id_user", type: "hidden"},
    {label: "id", name: "id", type: "hidden"},
  ]
});

$(editor_attributi.field('id_attributo').input()).on('change', function (e, d) {
  if(editor_attributi.display() == 'inline')
        return;
  update_valore_field_type(editor_attributi.field('id_attributo').val());
});

editor_attributi.on('initEdit', function ( e, node, data, items, type ) {
  if(type == 'inline') return;
  update_valore_field_type(data['id']);
} );

$('#attributoTable').on('click', 'tbody td', function (e) {
  // if(editor_attributi.display() == 'inline') return;
  // if("{{ !Auth::user()->can('edit-attributo') }}") return;
  // console.log(this.parentNode);
  // update_valore_field_type(this.parentNode.id);
  //
  // editor_attributi.inline(this, {
  //    onBlur: 'submit',
  //    submit: 'allIfChanged'
  //  });
});

// Edit record
$('#attributoTable').on('click', 'button#editor_edit', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-attributo') }}") return;
  $('#modal_attributi').modal('hide');
  editor_attributi.edit( $(this).closest('tr'), {
    title: 'Modifica',
    buttons: 'Aggiorna'
  });
});

// Delete a record
$('#attributoTable').on('click', 'button#editor_remove', function (e) {
  e.preventDefault();
  if("{{ !Auth::user()->can('edit-attributo') }}") return;
  editor_attributi.remove( $(this).closest('tr'), {
    title: 'Cancella record',
    message: 'Sei sicuro di voler eliminare l\'attributo selezionato?',
    buttons: 'Cancella attributo'
  });
});

editor_attributi.on('postSubmit', function( e, data, action ){
  $('#modal_attributi').modal('show');
});


});

function create_attribute(id_user){
  if("{{ !Auth::user()->can('edit-attributo') }}") return;
  editor_attributi.create({
    title: 'Aggiungi nuovo attributo',
    buttons: 'Salva'
  });
  editor_attributi.field('id_user').set(id_user);

}


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

function edit_attributi(id_user){
  $('#attributoTable').DataTable({
    dom: "lftipr",
    responsive: true,
    processing: true,
    serverSide: true,
    destroy: true,
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: {
      url: "{!! route('attributouser.data') !!}",
      data: {
        id_user: id_user
      }
    },
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'attributo_label', editField: 'id_attributo'},
      { data: 'valore_label', editField: 'valore'},
      { data: 'action', orderable: false, searchable: false}
    ]
  });
  $('#button_create_attribute').click(function() {
    create_attribute(id_user);
  });

  $('#modal_attributi').modal('show');
}

function update_valore_field_type(id_attributo){
  $.ajax({
    type: "POST",
    async: false,
    dataType: "json",
    url: "{{ route('attributo.valore_field') }}",
    data: {
      id_attributo: id_attributo,
      _token : "{{csrf_token()}}",
    },
    success: function(data){
      editor_attributi.clear('valore');
      editor_attributi.add(data);
    },
    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
  });
}

</script>
@endpush
