<?php
//use Modules\Prodotto\Entities\Fornitore;
?>

<body>

  <h2 style="text-align: center">{{$group->nome}}</h2>
  <h3>Componenti del gruppo</h3>
  <p>Per aggiungere un componente, vai in Anagrafica, selezionalo e clicca su "Aggiungi ad un gruppo"</p>
  <table class="table table-bordered" id="componentiTable" style="width: 100%">
    <thead>
      <tr>
        <th>Id</th>
        <th>Utente</th>
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

  var editor_componenti = new $.fn.dataTable.Editor({
    ajax: {
      url: "{{route('groupusers.index')}}"
    },
    table: "#componentiTable",
    display: "lightbox",
    fields: []
  });


  // Delete a record
  $('#componentiTable').on('click', 'button#editor_remove', function (e) {
    e.preventDefault();
    editor_componenti.remove( $(this).closest('tr'), {
      title: 'Cancella record',
      message: 'Sei sicuro di voler eliminare l\'utente selezionato dal gruppo?',
      buttons: 'Cancella'
    } );
  } );

  $('#componentiTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    dom: '',
    language: { "url": "{{ asset('Italian.json') }}" },
    ajax: "{!! route('groupusers.data', ['id_group' => $group->id]) !!}",
    columns: [
      { data: 'id', name: 'id', visible: false},
      { data: 'user_label'},
      { data: 'action', orderable: false, searchable: false}
    ]
  });
});

</script>
