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
        <p class="lead">Elenco delle iscrizioni fatte da te o da qualche componente della tua famiglia</p>
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

          <table class="table table-bordered" id="subscriptionTable" style="width: 100%">
            <thead>
              <tr>
                <th>N.</th>
                <th>Evento</th>
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
      url: "{{route('iscrizioni.index')}}"
    },
    display: "lightbox",
    table: "#subscriptionTable",
    fields: [
      {label: "Consenso dati sanitari", name: "consenso_dati_sanitari", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Consenso affiliazione", name: "consenso_affiliazione", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
      {label: "Consenso foto", name: "consenso_foto", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
    ]
  });

  $('#subscriptionTable').on('click', 'tbody td', function (e) {
    //if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
    var row = table.cell(this).index().row;
    var data = table.row(row).data();
    if(data['confirmed'] == 1) return;
    editor.inline(this, {
      onBlur: 'submit',
      submit: 'allIfChanged'
    });
  });

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

  table = $('#subscriptionTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    lengthChange: true,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: 'lfrtip',
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: {
      url: "{!! route('iscrizioni.data') !!}",
      data: {
        id_user: "{{ $user->id }}"
      }
    },
    columns: [
      { data: 'id', name: 'id'},
      { data: 'event_label'},
      { data: 'user_label'},
      {
        data: 'confirmed', editField: 'confirmed',
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
    ]


  });


});

function load_iscrizione(id_subscription){
  $("#spec1").load("{!! url('iscrizioni/"+id_subscription+"') !!}");
  $('html,body').animate(
    {scrollTop: $("#nome_sub").offset().top },
    'slow');
  }

</script>
@endpush
