<?php
use Modules\User\Entities\User;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <h1><i class="fas fa-user" aria-hidden="true"></i> Anagrafica</h1>
    <p class="lead">Elenco degli utenti iscritti al tuo oratorio</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2" style="margin-left: 0%; width: 100%;">
      <div class="panel panel-default">
        <div class="panel-body">
          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          {!! Form::open(['route' => 'user.action', 'method' => 'POST' ]) !!}

          <a href="{{ route('user.create')}}" class="btn btn-s btn-primary"><i class="fas fa-user"></i> Aggiungi utente</a>
          <a href="{{ route('report.user')}}" class="btn btn-s btn-primary"><i class="fas fa-file-alt"></i> Report</a>
          Se selezionati:
          <select id='action' name='action'>
            <option value='group'>Aggiungi ad un gruppo</option>
            @if(Module::find('sms')!=null)
            <option value='sms'>Invia SMS</option>
            @endif
            @if(Module::find('email')!=null)
            <option value='email'>Invia Email</option>
            @endif
            @if(Module::find('telegram')!=null)
            <option value='telegram'>Invia Telegram</option>
            @endif
            @if(Module::find('whatsapp')!=null)
            <option value='whatsapp'>Invia Whatsapp</option>
            @endif
          </select>

          {!! Form::submit('Vai', ['class' => 'btn btn-primary']) !!}

          <br><br>

          <table class="table table-bordered" id="usersTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Foto</th>
                <th>Cognome</th>
                <th>Nome</th>
                <th>Telefono</th>
                <th>Email</th>
                <th>Data di nascita</th>
                <th>Luogo di nascita</th>
                <th>Sesso</th>
                <th>Residenza</th>
                <th>Indirizzo</th>
                <th>Seleziona</th>
                <th>Operazioni</th>
              </tr>
            </thead>
          </table>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>


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
<!-- END Modal2 -->


@endsection

@push('scripts')
<script>
function del_confirmation(){
  $("[data-toggle=confirmation]").confirmation({
    rootSelector: '[data-toggle=confirmation]',
    title: 'Sicuro di eliminare l\'utente selezionato?',
    btnOkLabel: 'Si, elimina!',
    btnOkIcon: 'fa fa-share',
    btnOkClass: 'btn-success',
    btnCancelLabel: 'Annulla',
    btnCancelIcon: 'fa fa-times',
    btnCancelClass: 'btn-danger'
  });
}
$(document).ready(function(){
  $('#usersTable').DataTable({
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
    ajax: "{!! route('user.data') !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'photo', name: 'users.photo' },
      { data: 'cognome', name: 'users.cognome' },
      { data: 'name', name: 'users.name' },
      { data: 'cell_number', name: 'users.cell_number' },
      { data: 'email', name: 'users.email' },
      { data: 'nato_il', name: 'users.nato_il' },
      { data: 'nato_a', name: 'users.nato_a' },
      { data: 'sesso', name: 'users.sesso', visible: false },
      { data: 'residente', name: 'users.residente', visible: false},
      { data: 'via', name: 'users.via', visible: false},
      { data: 'check', orderable: false, searchable: false},
      { data: 'action', orderable: false, searchable: false}
    ]


  });

  $('#usersTable').on('page.dt', function() {
    setTimeout(del_confirmation(), 500);
  });

  $('#usersTable').on('order.dt', function() {
    setTimeout(del_confirmation(), 500);
  });

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
@endpush
