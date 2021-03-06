<?php
use Modules\Event\Entities\Week;
use Modules\User\Entities\User;
use Modules\Event\Entities\Event;
$weeks = Week::select('id', 'from_date', 'to_date')->where('id_event', $event->id)->orderBy('from_date', 'asc')->get();
$weeks_json = Week::where('id_event', $event->id)->orderBy('from_date', 'asc')->pluck('id')->toArray();
array_push($weeks_json, 0); //id della tabella specifiche generali
$weeks_json = json_encode($weeks_json);
?>


<!-- Modal2 -->
<div class="modal fade" id="eventspecsOp" tabindex="-1" role="dialog" aria-labelledby="EventSpecsOperation">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Aggiungi Specifica</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        <p>1) Scegli se vuoi inserire una specifica generale o settimanale</p>
        <?php
        $options = "<option value='0'>Generale</option>";
        foreach($weeks as $w){
          $options .= "<option value=".$w->id.">Settimana dal ".$w->from_date." al ".$w->to_date."</option>";
        }
        ?>
        <select id="valid_for" onchange="change_eventspec(this, {{ $event->id }})" class="form-control"><?php echo $options; ?></select>
        <p>2) Quale specifica?</p>
        <select id="event_spec" class="form-control"></select><br>

        <i onclick="add_eventspec({{ $subscription->id }}, {{ $event->id }}, true)" class="btn btn-primary" style="width: 45%"><i class="fa fa-plus" aria-hidden="true"></i>Inserisci</i>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
<body>

  <div style="padding: 2px; padding-top: 10px; text-align: center; background: #90EE90; border-radius: 10px; margin-bottom: 5px; " id="nome_sub">
    <?php
    if($event->stampa_anagrafica==0){
      $array_specifiche = json_decode($event->spec_iscrizione);
      $anagrafica = EventSpecValue::where(['id_subscription' => $subscription->id])->whereIn('id_eventspec', $array_specifiche)->get();
      if(count($anagrafica)>0){
        $val = "";
        foreach($anagrafica as $a){
          $val .= $a->valore." ";
        }
        echo "<h2>".$val."</h2>";
      }else{
        echo "<i style='font-size:12px;'>Specifica non esistente!</i>";
      }
    }else{
      try{
        echo "<h2>".User::find($subscription->id_user)->full_name."</h2>";
      }catch(\Exception $e){
        echo "<h2>Utente non esistente</h2>";
      }
    }
    ?>
  </div>


  <h2>Informazioni generali</h2>
  <table class="table table-bordered" id="spec_table_0" style="width: 100%">
    <thead>
      <tr>
        <th>Id</th>
        <th style='width: 32%;'>Specifica</th>
        <th>Valore</th>
        <th>Costo</th>
        <th>Acconto</th>
        <th>Pagato</th>
        <th></th>
      </tr>
    </thead>
  </table>
  <br><br>

  @if(count($weeks)>0)
  <h2>Informazioni settimanali</h2>

  @foreach($weeks as $w)
  <h3>Settimana {{ $w->from_date}} - {{ $w->to_date}}</h3>
  <table class='table table-bordered' id="spec_table_{{$w->id}}" style="width: 100%">
    <thead>
      <tr>
        <th>ID</th>
        <th style='width: 32%;'>Specifica</th>
        <th>Valore</th>
        <th>Costo</th>
        <th>Acconto</th>
        <th>Pagato</th>
        <th></th>
      </tr>
    </thead>
  </table>
  <br>
  @endforeach  <!-- foreach settimane-->


  @endif<!--  endif numero di settimane > 0-->

  <button style="font-size: 15px; width: 49%;" class="btn btn-primary btn-sm" onclick="aggiungi_specifica();">
    <i class="fa fa-plus"></i>Aggiungi specifica mancante
  </button>
</body>

<script>
var editors = [];
var tables = [];

$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  // Tabelle delle specifiche settimanali
  var weeks = JSON.parse("{{ $weeks_json }}");
  $.each(weeks, function(index, id_week){
    $('#spec_table_'+id_week).DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      rowId: 'DT_RowId',
      dom: 'rtip',
      language: { "url": "{{ asset('Italian.json') }}" },
      ajax: {
        url: "{!! route('eventspecvalues.data') !!}",
        data: {
          id_iscrizione: "{{ $subscription->id }}",
          id_week: id_week
        }
      },
      columns: [
        { data: 'id', name: 'id', visible: false},
        { data: 'specifica_label'},
        { data: 'valore_label', editField: 'valore', className: "dt-body-center"},
        { data: 'costo', render: function ( data, type, row ){
          return data+" €";
        }
      },
      { data: 'acconto', render: function ( data, type, row ){
        return data+" €";
      }
    },
    {
      data:   "pagato",
      render: function ( data, type, row ) {
        if (type === 'display') {
          if(row['costo'] == 0){
            return "";
          }
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
    { data: 'action', orderable: false, searchable: false, visible: "{{ Auth::user()->can('edit-iscrizioni') }}"}
  ]
});

//salvo l'editor in un array per i trigger successivi
editors[id_week] = new $.fn.dataTable.Editor({
  ajax: {
    url: "{{ route('eventspecvalues.index') }}"
  },
  table: '#spec_table_'+id_week,
  display: "lightbox",
  fields: [
    {label: "ID", name: "id"},
    {label: "id_week", name: "id_week", default: id_week},
    {label: "id_eventspec", name: "id_eventspec"},
    {label: "id_subscription", name: "id_subscription", default: "{{ $subscription->id}}"},
    {label: "Valore", name: "valore"}, //questa field viene aggiornata prima di aprire l'editor con il type corrispondente della specifica
    {label: "Costo", name: "costo", attr: {type: "number"}, default: 0},
    {label: "Acconto", name: "acconto", attr: {type: "number"}, default: 0},
    {label: "Pagato", name: "pagato", type:"checkbox", options: [{label:'', value:1}], separator: "", unselectedValue: 0},
  ]
});

//inline. Prima di aprire, aggiorno il type di "valore" con ajax
$('#spec_table_'+id_week).on('click', 'tbody td:not(:last-child)', function (e) {
  if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
  if(editors[id_week].display() == 'inline') return;
  id_eventspecvalue = this.parentNode.id;
  $.ajax({
    type: "POST",
    async: false,
    url: "{{ route('eventspecvalues.valore_field') }}",
    data: {
      id_eventspecvalue: id_eventspecvalue,
      _token : "{{csrf_token()}}",
    },
    success: function(data2){
      editors[id_week].clear('valore');
      editors[id_week].add(JSON.parse(data2));
    },
    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
  });

  editors[id_week].inline(this, {
    onBlur: 'submit',
    submit: 'allIfChanged'
  })
});

$(editors[id_week].field('pagato').node()).on('change', function () {
  var pagato = editors[id_week].field( 'pagato' ).val();
  if(pagato == 1) {
    editors[id_week].disable('costo');
    editors[id_week].disable('acconto');
  }else{
    editors[id_week].enable('costo');
    editors[id_week].enable('acconto');
  }
}
);

// Delete a record
$('#spec_table_'+id_week).on('click', 'button#editor_remove', function (e) {
  if("{{ !Auth::user()->can('edit-iscrizioni') }}") return;
  e.preventDefault();
  editors[id_week].remove( $(this).closest('tr'), {
    title: 'Cancella record',
    message: 'Sei sicuro di voler eliminare la riga selezionata?',
    buttons: 'Cancella riga'
  } );
} );

});
});

function change_eventspec(sel, id_event){
  $.get("{{ url('eventspec/dropdown')}}",
  {id_week: sel.value,
    id_event: id_event },
    function(data){
      var model = $("#event_spec");
      model.empty();
      $.each(data, function(index, element) {
        var prices = JSON.parse(element.price);
        model.append("<option value='"+ element.id +"' data-price='"+prices[sel.value]+"' data-type='"+element.id_type+"'>" + element.label + "</option>");
      });
    });
  }

  function add_eventspec(id_sub, id_event, admin){
    var valid_for = $('#valid_for').val(); //è l'id_week
    var event_spec = $('#event_spec').val();
    editors[valid_for].create(false)
    .set('id_week', valid_for)
    .set('id_eventspec', event_spec)
    .submit();
    $('#eventspecsOp').modal('hide');
  }

  function aggiungi_specifica(){
    $("#valid_for").trigger("change");
    $("#eventspecsOp").modal('show');
  }


</script>
