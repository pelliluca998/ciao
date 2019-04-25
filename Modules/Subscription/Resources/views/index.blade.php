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
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-flag'></i> Iscrizioni</h1>
        <p class="lead">Elenco delle iscrizioni per l'evento {{ $event!=null?$event->nome:'corrente'}}</p>
        <hr>
      </div>
    </div>
  </div>


  <div class="row justify-content-center" style="margin-top: 20px;">

    <div class="col-6">
      <div class="card">
        <div class="card-body">
          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          {!! Form::open(['route' => 'subscription.action', 'method' => 'POST' ]) !!}

          <table class="table table-bordered" id="subscriptionTable" style="width: 100%">
            <thead>
              <tr>
                <th>N.</th>
                <th>Utente</th>
                <th>Confermata</th>
                <th>Tipo</th>
                <th>C. Dati sanitari</th>
                <th>C. Affiliazione</th>
                <th>C. Foto</th>
                <th></th>
              </tr>
            </thead>
          </table>
          {!! Form::close() !!}
        </div>
      </div>

    </div>

    <div class="col-6">
      <div class="card">
        <div class="card-body">

          <div id="spec1" class="panel-body">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>





@endsection

@push('scripts')
<script>
var table;

$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  var editor = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('subscription.index')}}"
    },
    display: "lightbox",
    table: "#subscriptionTable",
    fields: [
      {label: "Confermata", name: "confirmed", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Tipo", name: "type", type:"select", options: ['ADMIN', 'WEB']},
      {label: "Consenso dati sanitari", name: "consenso_dati_sanitari", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Consenso affiliazione", name: "consenso_affiliazione", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Consenso foto", name: "consenso_foto", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
    ]
  });

  $('#subscriptionTable').on('click', 'tbody td', function (e) {
    if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
    editor.inline(this, {
      onBlur: 'submit',
      submit: 'allIfChanged'
    });
  });

  // Edit record
  $('#subscriptionTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
    editor.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#subscriptionTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
    editor.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'iscrizione selezionata?',
      buttons: 'Cancella iscrizione'
    } );
  } );

  var buttons = [];
  if("{{ Auth::user()->can('edit-iscrizioni') }}"){
    buttons.unshift({
      text: '<i class="far fa-check-circle"></i> Approva selezionati',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('approva');
      }
    },
    {
      text: '<i class="fas fa-trash-alt"></i> Cancella selezionati',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        invia_utenti_selezionati('cancella');
      }
    }
  );
}
if("{{ Module::find('email') != null && Module::find('email')->enabled() }}"){
  buttons.push(
    {
      text: '<i class="fas fa-comment"></i> Contatta gli iscritti',
      className: 'btn btn-sm btn-primary',
      action: function ( e, dt, button, config ){
        window.location = "{{ route('subscription.contact') }}";
      }
    }
  );
}

table = $('#subscriptionTable').DataTable({
  responsive: true,
  processing: true,
  serverSide: false,
  lengthChange: true,
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
  dom: 'Blfrtip',
  language: { "url": "{{ asset('Italian.json') }}" },
  ajax: {
    url: "{!! route('subscription.data') !!}",
    data: {
      id_event: "{{ $event->id }}"
    }
  },
  columns: [
    { data: 'id', name: 'id'},
    { data: 'user_label'},
    {
      data: 'confirmed', name: 'subscriptions.confirmed', editField: 'confirmed',
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
    { data: 'type', name: 'subscriptions.type' },
    {
      data: 'consenso_dati_sanitari',
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
      data: 'consenso_affiliazione',
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
      data: 'consenso_foto',
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
    { data: 'action', orderable: false, searchable: false},
  ],
  select: {
    style:    'os',
    selector: 'td:first-child'
  },
  buttons: {
    dom: {
      button: {
        tag: 'button',
        className: ''
      }
    },
    buttons: buttons
  },


});

$('#subscriptionTable').on( 'mouseenter', 'tbody td:not(:last-child)', function () {
  var id = this.parentNode.id;
  $('#sub_'+id).popover('show');
});

$('#subscriptionTable').on( 'mouseout', 'tbody td:not(:last-child)', function () {
  var id = this.parentNode.id;
  $('#sub_'+id).popover('hide');
} );


});

function load_iscrizione(id_subscription){
  $("#spec1").load("{!! url('admin/subscription/"+id_subscription+"') !!}");
  $('html,body').animate(
    {scrollTop: $("#nome_sub").offset().top },
    'slow');
  }

  function invia_utenti_selezionati(action){
    var selected_rows = [];
    //costruisco l'array con tutti le iscrizioni selezionate
    $.each(table.rows('.selected').nodes(), function(i, item) {
      selected_rows.push(item.id);
    });
    if(selected_rows.length == 0){
      alert("Devi selezionare almeno un'iscrizione!");
      return;
    }

    //invio in POST l'array e faccio il redirect alla pagina corretta in  base all'azione selezionata

    $.ajax({
      type: "POST",
      url: "{{ route('subscription.action') }}",
      data: {
        check_user: JSON.stringify(selected_rows),
        action: action,
        _token : "{{csrf_token()}}",
      },
      success: function(data){
        window.location = "{{ route('subscription.index') }}";
      },
      error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
    });
  }

</script>
@endpush
