<?php
//use Modules\Prodotto\Entities\Fornitore;
?>

<body>

  <h2 style="text-align: center">{{$type->label}}</h2>
  <h3>Opzioni per l'elenco selezionato</h3>
  <table class="table table-bordered" id="opzioniTable" style="width: 100%">
    <thead>
      <tr>
        <th>Id</th>
        <th>Opzione</th>
        <th>Ordine</th>
        <th></th>
      </tr>
    </thead>
  </table>
  <br><br>


</body>

<script>

$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': '{{csrf_token()}}'
    }
  });

  var editor_opzioni = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('typeselect.index')}}"
    },
    table: "#opzioniTable",
    display: "lightbox",
    fields: [
      {label: "Opzione", name: "option"},
      {label: "Ordine", name: "ordine", attr: {type: 'number', min: 0, step: 1}},
      {label: "id_type", name: "id_type", type: 'hidden', default: "{{ $type->id }}"},
    ]
  });

  $('#opzioniTable').on('click', 'tbody td:not(:last-child)', function (e) {
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor_opzioni.inline(this, {
      onBlur: 'submit'
    });
  });

  // Edit record
  $('#opzioniTable').on('click', 'button#editor_edit', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor_opzioni.edit( $(this).closest('tr'), {
      title: 'Modifica',
      buttons: 'Aggiorna'
    });
  } );

  // Delete a record
  $('#opzioniTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    if("{{ !Auth::user()->can('edit-select') }}"){
      return;
    }
    editor_opzioni.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'opzioni selezionata?',
      buttons: 'Cancella'
    } );
  } );

var buttons = [];
//Permesso di aggiungere nuova opzioni
if("{{ Auth::user()->can('edit-select') }}"){
  buttons.unshift(
    {
      extend: 'create',
      editor: editor_opzioni,
      text: '<i class="fas fa-plus"></i> Nuova opzione',
      className: 'btn btn-sm btn-primary',
      formButtons: [
        {
          label: 'Annulla',
          fn: function () { this.close(); }
        },
        '<i class="fas fa-save"></i> Salva'
      ],
      formTitle: "Nuovo opzione",
      formMessage: ''
    }
  );
}

  $('#opzioniTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    dom: 'B',
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('typeselect.data', ['id_type' => $type->id]) !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'option'},
      { data: 'ordine'},
      { data: 'action', orderable: false, searchable: false, visible: "{{ Auth::user()->can('edit-select') }}"}
    ],
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
});

</script>
