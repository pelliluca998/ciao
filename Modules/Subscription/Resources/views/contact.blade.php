<?php
use Modules\Event\Entities\EventSpec;
use Modules\User\Entities\Group;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Attributo\Entities\Attributo;
use Modules\Event\Entities\Week;
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <h1><i class="fas fa-user" aria-hidden="true"></i> Contatta gli iscritti</h1>
    <p class="lead">Scegli quali utenti contattare per avvisarli circa l'evento corrente</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-body">
          Attraverso questa pagina puoi filtrare gli utenti iscritti al tuo evento a cui inviare un'email o un sms. Metti la spunta ai campi che vuoi filtrare e il contenuto del filtro, scegli come contattarli e poi clicca su "Invia".<br><br>

          {!! Form::open(['route' => 'subscription.contact_send']) !!}
          <?php
          $id_event=Session::get('work_event');
          ?>
          <div style="float: left; width: 100%; padding: 5px;">
            <h4>Passo 1: Scegli le infomazioni <b>riguardanti le settimane</b> da inserire nel report:</h4>
            <?php
            $weeks = Week::where('id_event', $id_event)->orderBy('from_date', 'ASC')->get();
            $w=0;

            foreach($weeks as $week){
              $index = 0;
              echo "<b>Settimana dal ".$week->from_date." al ". $week->to_date."</b><br>";
              //get campi per ogni settimana
              $specs = (new EventSpec)
              ->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for')
              ->where([['id_event', $id_event], ['event_specs.general', 0]])
              ->get();
              ?>
              <table class='testgrid draggable'>
                <tr>
                  <th style="width: 60%;">Campo</th>
                  <th>Filtra?</th>
                  <th>Valore del filtro</th>
                </tr>
                <tbody>


                  @foreach($specs as $spec)
                  <?php
                  $valid = json_decode($spec->valid_for, true);
                  ?>
                  @if($valid[$week->id]==1)
                  <tr>
                    <td>{{$spec->label}}</td>
                    <td>
                      <input name="week_filter[{{$w}}][{{$loop->index}}]" value="0" type="hidden"/>
                      <input name="week_filter[{{$w}}][{{$loop->index}}]" value="{{$spec->id}}" type="checkbox" class="form-control" onchange="disable_select(this, 'week_filter_value_{{$w}}_{{$index}}', true)"/>
                    </td>
                    <td>

                      {!! Form::hidden('week_filter_value['.$w.']['.$loop->index.']', 0) !!}

                      @if($spec->id_type>0)
                      {!! Form::select('week_filter_value['.$w.']['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "week_filter_value_".$w."_".$index])!!}
                      @else
                      @if($spec->id_type==-1)
                      {!! Form::text('week_filter_value['.$w.']['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "week_filter_value_".$w."_".$index]) !!}
                      @elseif($spec->id_type==-2)
                      {!! Form::hidden('week_filter_value['.$w.']['.$loop->index.']', 0) !!}
                      {!! Form::checkbox('week_filter_value['.$w.']['.$loop->index.']', 1, '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "week_filter_value_".$w."_".$index]) !!}
                      @elseif($spec->id_type==-3)
                      {!! Form::number('week_filter_value['.$w.']['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "week_filter_value_".$w."_".$index]) !!}
                      @endif
                      @endif

                    </td>
                  </tr>
                  <?php $index++; ?>
                  @endif

                  @endforeach
                </tbody>
              </table>
              <br>
              <?php
              $w++;
            }
            ?>

            <h4>Passo 2: Scegli quali altre colonne, <b>prese dalle Specifiche generali</b>, inserire nel report</h4>
            <?php
            $specs = (new EventSpec)
            ->select('event_specs.label', 'event_specs.id', 'event_specs.id_type as id_type')
            ->where([['event_specs.id_event', $id_event], ['event_specs.general', 1]])
            ->orderBy('event_specs.label', 'asc')
            ->get();
            ?>
            <table class='testgrid draggable'>
              <tr>
                <th style="width: 60%;">Specifica</th>
                <th>Filtra?</th>
                <th>Valore del filtro</th>
              </tr>
              <tbody>

                @foreach($specs as $spec)
                <tr>
                  <td>{{$spec->label}}</td>
                  <td>
                    <input name="spec_filter[{{$loop->index}}]" value="0" type="hidden"/>
                    <input name="spec_filter[{{$loop->index}}]" value="{{$spec->id}}" type="checkbox" class="form-control" onchange="disable_select(this, 'spec_filter_value_{{$loop->index}}', true)"/></td>
                    <td>

                      {!! Form::hidden('spec_filter_value['.$loop->index.']', 0) !!}

                      @if($spec->id_type>0)
                      {!! Form::select('spec_filter_value['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "spec_filter_value_".$loop->index])!!}
                      @else
                      @if($spec->id_type==-1)
                      {!! Form::text('spec_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "spec_filter_value_".$loop->index]) !!}
                      @elseif($spec->id_type==-2)
                      {!! Form::hidden('spec_filter_value['.$loop->index.']', 0) !!}
                      {!! Form::checkbox('spec_filter_value['.$loop->index.']', 1, '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "spec_filter_value_".$loop->index]) !!}
                      @elseif($spec->id_type==-3)
                      {!! Form::number('spec_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "spec_filter_value_".$loop->index]) !!}
                      @endif
                      @endif

                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>





              <h4>Passo 3: Scegli le infomazioni <b>anagrafiche degli utenti</b> da inserire nel report:</h4>

              <table class='testgrid' id=''>
                <thead><tr>
                  <th style='width: 60%;'>Specifica</th>
                  <th>Filtra?</th>
                  <th>Valore del filtro:</th>
                </tr></thead>

                <?php

                $c = [];
                $c[] = ['id'=>'email', 'label'=>'Email'];
                $c[] = ['id'=>'cell_number', 'label'=>'Numero Cell.'];
                $c[] = ['id'=>'username', 'label'=>'Username'];
                $c[] = ['id'=>'nato_il', 'label'=>'Data di nascita'];
                $c[] = ['id'=>'nato_a', 'label'=>'Luogo di nascita'];
                $c[] = ['id'=>'sesso', 'label'=>'Sesso'];
                $c[] = ['id'=>'residente', 'label'=>'Residenza'];
                $c[] = ['id'=>'via', 'label'=>'Indirizzo'];

                ?>

                @foreach($c as $column)
                <tr>

                  <td>{{$column['label']}}</td>
                  <td><input type='hidden' name="user_filter[{{$loop->index}}]" value="0"/>
                    <input name="user_filter[{{$loop->index}}]" value="1" type="checkbox" class="form-control" onchange="disable_select(this, 'user_filter_value_{{$loop->index}}', true)"/></td>
                    <td>
                      <input name="user_filter_id[{{$loop->index}}]" type="hidden" value="{{$column['id']}}"/>

                      {!! Form::text('user_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "user_filter_value_".$loop->index]) !!}

                    </td>
                  </tr>
                  @endforeach
                </table><br>


                <h4>Passo 4: Scegli gli attributi degli utenti da inserire nel report:</h4>

                <table class='testgrid' id=''>
                  <thead><tr>
                    <th style='width: 60%;'>Attributo</th>
                    <th>Filtra?</th>
                    <th>Valore del filtro:</th>
                  </tr></thead>
                  <?php
                  $attributos = Attributo::select('attributos.*')->where('attributos.id_oratorio', Session::get('session_oratorio'))->orderBy('ordine', 'ASC')->get();
                  ?>
                  @foreach($attributos as $a)
                  <tr>
                    <td>{{$a->nome}}</td>
                    <td>
                      <input type="hidden" name="att_filter[{{$loop->index}}]" value="0"/>
                      <input name="att_filter[{{$loop->index}}]" value="1" type="checkbox" class="form-control" onchange="disable_select(this, 'att_filter_value_{{$loop->index}}', true)"/>
                    </td>
                    <td>
                      @if($a->id_type>0)
                      {!! Form::select('att_filter_value['.$loop->index.']', TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index])!!}
                      @else
                      @if($a->id_type==-1)
                      {!! Form::text('att_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
                      @elseif($a->id_type==-2)
                      {!! Form::hidden('att_filter_value['.$loop->index.']', 0) !!}
                      {!! Form::checkbox('att_filter_value['.$loop->index.']', 1, '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
                      @elseif($a->id_type==-3)
                      {!! Form::number('att_filter_value['.$loop->index.']', '', ['class' => 'form-control', 'disabled' => 'true', 'id' => "att_filter_value_".$loop->index]) !!}
                      @endif
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </table><br>


                <div class="form-group">
                  <h4>Come vuoi contattare gli utenti?</h4>
                  {!! Form::radio('type', 'sms', true) !!} SMS<br>
                  {!! Form::radio('type', 'email', false) !!} Email<br>
                  {!! Form::radio('type', 'telegram', false) !!} Telegram<br>
                  {!! Form::radio('type', 'whatsapp', false) !!} WhatsApp<br>
                </div>

                <div class="form-group">
                  {!! Form::submit('Invia!', ['class' => 'btn btn-primary form-control']) !!}
                  {!! Form::close() !!}
                </div>








              </div>



            </div>

          </div>
        </div>
      </div>
    </div>

    @endsection
