<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
use Modules\Sms\Entities\SmsLog;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\IdFieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use Nayjest\Grids\ObjectDataRow;
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <h1><i class="fab fa-whatsapp" aria-hidden="true"></i> Messaggi WhatsApp</h1>
    <p class="lead">Elenco dei messaggi inviati e ricevuti</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
      @if(Session::has('flash_message'))
      <div class="alert alert-success">
        {{ Session::get('flash_message') }}
      </div>
      @endif
      <div class="panel panel-default panel-left">
        <div class="panel-heading">Messaggi inviati</div>
        <div class="panel-body">
          <table class="table table-bordered" id="sendTable" style="width: 100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Destinatario</th>
                <th>Messaggio</th>
                <th>Esito</th>
                <th>Data</th>
                <th>Operazioni</th>
              </tr>
            </thead>
          </table>

        </div>
      </div>

      <div class="panel panel-default panel-right">
        <div class="panel-heading">Messaggi ricevuti</div>
        <div class="panel-body">
          <table class="table table-bordered" id="receiveTable" style="width: 100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Mittente</th>
                <th>Messaggio</th>
                <th>Data</th>
                <th>Operazioni</th>
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
  $('#sendTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    pageLength: 50,
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
    ajax: "{!! route('whatsapp.send_data') !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'id_user', name: 'whatsapp.id_user' },
      { data: 'testo', name: 'whatsapp.testo' },
      { data: 'esito', name: 'whatsapp.esito' },
      { data: 'created_at', name: 'whatsapp.created_at' },
      { data: 'action', orderable: false, searchable: false}
    ]


  });

  $('#receiveTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    pageLength: 50,
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
    ajax: "{!! route('whatsapp.receive_data') !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'id_user', name: 'whatsapp.id_user' },
      { data: 'message_body', name: 'whatsapp.message_body' },
      { data: 'created_at', name: 'whatsapp.created_at' },
      { data: 'action', orderable: false, searchable: false}
    ]


  });

});

</script>
@endpush
