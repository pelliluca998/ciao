<?php
use Modules\Subscription\Entities\Subscription;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSpecValue;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <h1><i class="fas fa-flag" aria-hidden="true"></i> Iscrizioni</h1>
    <p class="lead">Elenco delle iscrizioni per l'evento corrente</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2" style="margin-left: 0%; width: 100%;">
      <div class="panel panel-default panel-left">
        <div class="panel-body">
          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          {!! Form::open(['route' => 'subscription.action', 'method' => 'POST' ]) !!}

          <a href="{{ route('subscription.contact')}}" class="btn btn-s btn-primary"><i class="fas fa-comment"></i> Contatta tutti gli iscritti</a>
          Se selezionati:
          <select id='action' name='action'>
            <option value='approva'>Approva</option>
            <option value='cancella'>Cancella</option>
          </select>

          {!! Form::submit('Vai', ['class' => 'btn btn-primary']) !!}

          <br><br>

          <table class="table table-bordered" id="subscriptionTable" style="width: 100%">
            <thead>
              <tr>
                <th>Id</th>
                <th>Utente</th>
                <th>Confermata</th>
                <th>Tipo</th>
                <th>Check</th>
                <th>Edit</th>
                <th>Apri iscrizione</th>
              </tr>
            </thead>            
          </table>
          {!! Form::close() !!}
        </div>

      </div>

      <div class="panel panel-default panel-right">
        <div class="panel-heading">Specifiche iscrizione</div>
        <div class="panel-body">

          <div id="spec1" class="panel-body">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal2 -->
<div class="modal fade" id="subOp" tabindex="-1" role="dialog" aria-labelledby="SubscriptionOperation">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Operazioni Iscrizione</h4>
      </div>
      <?php
      $buttons = array(
        ["label" => "Modifica iscrizione",
        "desc" => "",
        "url" => "subscription.edit",
        "class" => "btn-primary",
        "icon" => ""],
        ["label" => "Stampa iscrizione",
        "desc" => "",
        "url" => "subscription.print",
        "class" => "btn-primary",
        "icon" => ""],
        ["label" => "Elimina iscrizione",
        "desc" => "L'operazione è irreversibile!",
        "url" => "subscription.destroy",
        "class" => "btn-danger",
        "icon" => ""]
      );
      ?>

      <div class="modal-body">

        Operazioni disponibili per l'iscrizione di  <b><span id="name"></span></b>:
        @foreach ($buttons as $button)
        <div style="margin: 5px;">
          {!! Form::open(['route' => $button['url'], 'method' => 'GET']) !!}
          {!! Form::hidden('id_subscription', '0', ['id' => 'id_sub']) !!}
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





@endsection

@push('scripts')
<script>
function del_confirmation(){
  $("[data-toggle=confirmation]").confirmation({
    rootSelector: '[data-toggle=confirmation]',
    title: 'Sicuro di eliminare l\'iscrizione selezionata?',
    btnOkLabel: 'Si, elimina!',
    btnOkIcon: 'fa fa-share',
    btnOkClass: 'btn-success',
    btnCancelLabel: 'Annulla',
    btnCancelIcon: 'fa fa-times',
    btnCancelClass: 'btn-danger'
  });
}
$(document).ready(function(){
  var table = $('#subscriptionTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    lengthChange: true,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: 'lBfrtip',
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
    ajax: "{!! route('subscription.data') !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'id_user', name: 'id_user' },
      { data: 'confirmed', name: 'subscriptions.confirmed' },
      { data: 'type', name: 'subscriptions.type' },
      { data: 'check', orderable: false, searchable: false},
      { data: 'action', orderable: false, searchable: false},
      { data: 'specs', orderable: false, searchable: false}
    ],


  });



  $('#subscriptionTable').on('page.dt', function() {
    setTimeout(del_confirmation(), 500);
  });

  $('#subscriptionTable').on('order.dt', function() {
    setTimeout(del_confirmation(), 500);
  });

  $('#subOp').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var name = button.data('name') // Extract info from data-* attributes
    var subid = button.data('subid');
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this);
    modal.find('#name').text(name);
    modal.find("[id*='id_sub']").val(subid);
  });
});

</script>
@endpush
