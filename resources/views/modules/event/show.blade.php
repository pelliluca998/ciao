<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <h1><i class="fas fa-calendar-alt" aria-hidden="true"></i> Eventi</h1>
    <p class="lead">Elenco degli eventi che stai organizzando</p>
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

          <a href="{{ route('events.create')}}" class="btn btn-s btn-primary"><i class="fas fa-plus"></i> Aggiungi evento</a><br><br>

          <table class="table table-bordered" id="eventsTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Nome evento</th>
                <th>Anno</th>
                <th>Descrizione</th>
                <th>Attivo</th>
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
<div class="modal fade" id="eventOp" tabindex="-1" role="dialog" aria-labelledby="EventOperation">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Operazioni Evento</h4>
      </div>
      <?php
      $buttons = array(
        ["label" => "Modifica informazioni",
        "desc" => "",
        "url" => "events.edit",
        "class" => "btn-primary",
        "toggle" => "",
        "icon" => ""],
        ["label" => "Lavora con questo evento",
        "desc" => "",
        "url" => "events.work",
        "class" => "btn-primary",
        "toggle" => "",
        "icon" => ""],
        ["label" => "Vedi iscrizioni",
        "desc" => "",
        "url" => "subscription.event",
        "toggle" => "",
        "class" => "btn-primary",
        "icon" => ""],
        ["label" => "Specifiche",
        "desc" => "",
        "url" => "eventspecs.show",
        "toggle" => "",
        "class" => "btn-primary",
        "icon" => ""],
        ["label" => "Clona",
        "desc" => "",
        "url" => "events.clone",
        "class" => "btn-primary",
        "toggle" => "",
        "icon" => ""],
        ["label" => "Elimina evento",
        "desc" => "L'operazione è irreversibile!",
        "url" => "events.destroy",
        "class" => "btn-danger",
        "toggle" => "confirmation",
        "icon" => ""]
      );
      ?>

      <div class="modal-body">

        Operazioni disponibili per l'evento <b><span id="name"></span></b>:
        @foreach ($buttons as $button)
        <div style="margin: 5px;">
          {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
          {!! Form::hidden('id_event', '0', ['id' => 'id_event']) !!}
          {!! Form::submit($button['label'], ['class' => 'btn '.$button['class'], 'data-toggle' => $button['toggle']]) !!}
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
    title: 'Sicuro di eliminare l\'evento selezionato?',
    btnOkLabel: 'Si, elimina!',
    btnOkIcon: 'fa fa-share',
    btnOkClass: 'btn-success',
    btnCancelLabel: 'Annulla',
    btnCancelIcon: 'fa fa-times',
    btnCancelClass: 'btn-danger'
  });
}
$(document).ready(function(){
  $('#eventsTable').DataTable({
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
    ajax: "{!! route('events.data') !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'nome', name: 'events.nome' },
      { data: 'anno', name: 'events.anno' },
      { data: 'descrizione', name: 'events.descrizione' },
      { data: 'active', name: 'users.active' },
      { data: 'action', orderable: false, searchable: false}
    ]


  });

  $('#eventsTable').on('page.dt', function() {
    setTimeout(del_confirmation(), 500);
  });

  $('#eventsTable').on('order.dt', function() {
    setTimeout(del_confirmation(), 500);
  });
  $('#eventOp').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var name = button.data('name') // Extract info from data-* attributes
    var eventid = button.data('eventid');
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this);
    modal.find('#name').text(name);
    modal.find("[id*='id_event']").val(eventid);
  });
});
</script>
@endpush
