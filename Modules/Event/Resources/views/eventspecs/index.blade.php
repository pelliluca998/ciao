<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\Type;
use App\TypeBase;
use Modules\Event\Entities\Week;
use Modules\Contabilita\Entities\Cassa;
use Modules\Contabilita\Entities\ModoPagamento;
use Modules\Contabilita\Entities\TipoPagamento;
use App\License;

$contabilita = Module::has('contabilita')?true:false;

?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
		<div class="col">
			<div class="card bg-transparent border-0">
				<h1><i class="fas fa-stream"></i> Specifiche Evento</h1>
				<p class="lead">Informazioni aggiuntive per l'evento</p>
				<hr>
			</div>
		</div>
	</div>

  @if(Session::has('flash_message'))
  <div class="alert alert-success">
    {{ Session::get('flash_message') }}
  </div>
  @endif

  <div class="row justify-content-center">
		<div class="col">
			<div class="card">
				<div class="card-body">

          <p>In questa pagina puoi aggiungere o modificare le informazioni che verranno chieste all'utente in fase di registrazione all'evento. <a href="http://doc.segresta.it/eventi.html#specifiche-evento" target="_blank">Guida alla compilazione</a></p>

          {!! Form::open(['route' => 'eventspecs.save']) !!}
          <table class="table table-bordered" id="showeventspecs">

            <?php
            $specs = (new EventSpec)->where('id_event', $id_event)->orderBy('ordine', 'ASC')->get();
            $weeks = Week::where('id_event', $id_event)->orderBy('from_date', 'asc')->get();
            $index=0;
            ?>
            <thead>
              <tr>
                <th>Sposta</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th>Tipo</th>
                <th>Obbligatoria</th>
                <th>Generale</th>
                @foreach($weeks as $w)
                <th>Settimana <br>{{$w->from_date}}</th>
                @endforeach
                <th>Nascosta</th>
                <th>Del</th>
                @if($contabilita)
                <th>Contabilità</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($specs as $a)
              <?php
              $valid = json_decode($a->valid_for, true);
              $price = json_decode($a->price, true);
              $acconto = json_decode($a->acconto, true);
              $price_0 = 0; //prezzo per la colonna generale
              $acconto_0 = 0; //acconto per la colonna generale
              if(isset($price[0])){
                $price_0 = $price[0];
              }

              if(isset($acconto[0])){
                $acconto_0 = $acconto[0];
              }

              ?>
              <!--  L'id della riga serve solo per l'eliminazione-->
              <tr id="row_{{$loop->index}}">
                {!! Form::hidden('id_spec[]', $a->id) !!}
                {!! Form::hidden('event[]', $id_event) !!}
                <td><i class="fas fa-arrows-alt fa-2x" aria-hidden="true"></i></td>
                <td>
                  {!! Form::text("label[]", $a->label, ['class' => 'form-control', 'style' => 'width: 100%']) !!}
                </td>
                <td>
                  {!! Form::text("descrizione[]", $a->descrizione, ['class' => 'form-control', 'style' => 'width: 100%']) !!}
                </td>
                <td>
                  {!! Form::select("id_type[]", Type::getTypes(), $a->id_type, ['class' => 'form-control']) !!}
                </td>
                <td>
                  {!! Form::hidden('obbligatoria[]', 0) !!}
                  {!! Form::checkbox("obbligatoria[]", $a->id, $a->obbligatoria, ['id' => "obbligatoria_".$a->id, 'class' => 'form-control']) !!}
                </td>
                <td>
                  {!! Form::hidden('general[]', 0) !!}
                  {!! Form::checkbox("general[]", $a->id, $a->general, ['id' => "general_".$a->id, 'class' => 'form-control', "onclick" => "check_week($a->id, 0, true)"]) !!}
                  <br>
                  Prezzo: {!! Form::number("price[".$a->id."][0]", $price_0, ['class' => 'form-control', 'style' => 'width: 90px;', 'step' => '0.01']) !!}<br>
                  Acconto: {!! Form::number("acconto[".$a->id."][0]", $acconto_0, ['class' => 'form-control', 'style' => 'width: 90px;', 'step' => '0.01']) !!}
                </td>
                @foreach($weeks as $w)
                <td>

                  {!! Form::hidden("valid_for[".$a->id."][".$w->id."]", 0) !!}
                  @if(isset($valid[$w->id]))
                  {!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, $valid[$w->id], ['id' => "check_".$a->id."_".$w->id, 'class' => 'form-control', "onclick" => "check_week($a->id, $w->id, false)"]) !!}
                  @else
                  {!! Form::checkbox("valid_for[".$a->id."][".$w->id."]", 1, 0, ['id' => "check_".$a->id."_".$w->id, 'class' => 'form-control', "onclick" => "check_week($a->id, $w->id, false)"]) !!}
                  @endif
                  <br>
                  @php
                  $price_w = 0;
                  if(isset($price[$w->id])){
                    $price_w = $price[$w->id];
                  }

                  $acconto_w = 0;
                  if(isset($acconto[$w->id])){
                    $acconto_w = $acconto[$w->id];
                  }
                  @endphp
                  Prezzo: {!! Form::number("price[".$a->id."][".$w->id."]", $price_w, ['class' => 'form-control', 'style' => 'width: 90px;', 'step' => '0.01']) !!}<br>
                  Acconto: {!! Form::number("acconto[".$a->id."][".$w->id."]", $acconto_w, ['class' => 'form-control', 'style' => 'width: 90px;', 'step' => '0.01']) !!}
                </td>
                @endforeach



                <td>
                  {!! Form::hidden('hidden[]', 0) !!}
                  {!! Form::checkbox("hidden[]", $a->id, $a->hidden, ['class' => 'form-control']) !!}
                </td>
                <td>
                  <button onclick="eventspec_destroy({{$a->id}}, {{$loop->index}})" style="font-size: 15px;" type='button' class="btn btn-primary btn-sm" ><i class="fa fa-trash fa-2x" aria-hidden="true"></i></button>
                </td>
                @if($contabilita)
                <td>

                  <?php
                  $cassa = Cassa::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id');
                  $modo = ModoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id');
                  $tipo = TipoPagamento::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id', 'ASC')->pluck('label', 'id');
                  ?>
                  <div style="width: 100%; margin-bottom: 40px;">
                    <div style="float:left; margin-right: 2px; width: 100%">Cassa:</div>
                    <div style="float:left;">
                      @if(count($cassa)>0)
                      {!! Form::select("cassa[]", $cassa, $a->id_cassa, ['class' => 'form-control']) !!}
                      @else
                      {!! Form::hidden('cassa[]', 0) !!}
                      @endif
                    </div>
                  </div>
                  <div style="width: 100%; margin-bottom: 80px;">
                    <div style="float:left; margin-right: 2px; width: 100%">Modalità:</div>
                    <div style="float:left;">
                      @if(count($modo)>0)
                      {!! Form::select("modo_pagamento[]", $modo, $a->id_modopagamento, ['class' => 'form-control']) !!}
                      @else
                      {!! Form::hidden('modo_pagamento[]', 0) !!}
                      @endif
                    </div>
                  </div>
                  <div style="width: 100%; margin-bottom: 50px;">
                    <div style="float:left; margin-right: 2px; width: 100%">Tipologia:</div>
                    <div style="float:left;">
                      @if(count($tipo)>0)
                      {!! Form::select("tipo_pagamento[]", $tipo, $a->id_tipopagamento, ['class' => 'form-control']) !!}
                      @else
                      {!! Form::hidden('tipo_pagamento[]', 0) !!}
                      @endif
                    </div>
                  </div>
                </td>
                @else
                {!! Form::hidden('cassa[]', 0) !!}
                {!! Form::hidden('modo_pagamento[]', 0) !!}
                {!! Form::hidden('tipo_pagamento[]', 0) !!}
                @endif

              </tr>

              @endforeach

            </tbody>

          </table><br><br>
          {!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 33%']) !!}
          <i onclick='eventspecs_add({{$id_event}});' class='btn btn-primary' style='width: 33%'>
            <i class='fa fa-plus' aria-hidden='true'></i>Aggiungi specifica
          </i>
          <a href="{{ route('subscription.print', ['id_subscription' => 'preview'])}}" class='btn btn-primary' style='width: 33%'>Anteprima modulo d'iscrizione</a>
          {!! Form::close() !!}

        </div>
      </div>
    </div>
  </div>
</div>

<script>
function check_week(a, w, general){
  if(general && $('#general_'+a).is(':checked')){
    //setto tutte le settimane !check
    var weeks = $('[id^=check_'+a+']').prop('checked', false);
  }else{
    if($('#check_'+a+'_'+w).is(':checked')){
      var general = $('[id=general_'+a+']').prop('checked', false);
    }
  }
}

function eventspecs_add(id_event){
  var row = "<tr>";
  row += "<input name='id_spec[]' type='hidden' value='0'/>";
  row += "<input name='hidden[]' type='hidden' value='0'/>";
  row += "<input name='event[]' type='hidden' value='"+id_event+"'/>";
  //Sposta
  row += "<td><i class='fas fa-arrows-alt fa-2x'></td>";
  //Nome
  var form = ('{{ Form::text("label[]", "", ["class" => "form-control", "style" => "width: 100%"]) }}').replace(/"/g, '\'');
  row += "<td>"+form+"</td>";
  //Descrizione
  form = ('{{ Form::text("descrizione[]", "", ["class" => "form-control", "style" => "width: 100%"]) }}').replace(/"/g, '\'');
  row += "<td>"+form+"</td>";
  //tipo
  var select = ('{{ Form::select("id_type[]", Type::getTypes(), null, ["class" => "form-control"]) }}').replace(/"/g, '\'');
  row += "<td>"+select+"</td>";
  //Generale
  row += "<td></td>";
  row += "<td></td>";
  row += "<td>";

  row += "</td>";
  row += "<td></td>";
  row += "</tr>";

  $('#showeventspecs tr:last').after(row);
  //$('#contatore').val((t+1));

}



$(document).ready(function(){
  // Drag adn Drop delle righe della tabella
  var fixHelper = function(e, ui) {
    ui.children().each(function() {
      $(this).width($(this).width());
    });
    return ui;
  };

  $("#showeventspecs tbody").sortable({
    stop: function(event, ui){
    }
  }).disableSelection();

});
</script>
@endsection
