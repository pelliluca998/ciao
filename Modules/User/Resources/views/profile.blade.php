<?php
use Modules\User\Entities\User;
use Modules\User\Entities\Group;
use App\Role;
use App\RoleUser;
use App\Nazione;
use App\Provincia;
use App\Comune;
use Modules\Attributo\Entities\Attributo;
use Modules\Attributo\Entities\AttributoUser;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Oratorio\Entities\Type;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-user'></i> Il tuo profilo</h1>
        <p class="lead">Modifica e salva il tuo profilo, oppure <a href="{{ route('home') }}">torna alla pagina principale.</a></p>
        <hr>
      </div>
    </div>
  </div>


  <div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <?php
          $user->id_role=RoleUser::where('user_id', $user->id)->first()->role_id;
          ?>
          {!! Form::model($user, ['method' => 'PATCH','files' => true,'route' => ['user.updateprofile', $user->id]]) !!}
          <h3>Informazioni generali</h3>
          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('name', 'Nome') !!}
              {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('cognome', 'Cognome') !!}
              {!! Form::text('cognome', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('sesso', 'Sesso') !!}
              {!! Form::select('sesso', array('M' => 'Uomo', 'F' => 'Donna'), null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('email', 'Email') !!}
              {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('cell_number', 'Numero cellulare') !!}
              {!! Form::text('cell_number', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('path_photo', 'Foto Profilo') !!}<br>
              <?php
              if($user->photo!=''){
                echo "<img src='".url(Storage::url('public/'.$user->photo))."' width=200px/>";
              }else{
                echo "Nessuna foto caricata!<br><br>";
              }
              ?>
              <br>
              {!! Form::file('path_photo', null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('cod_fiscale', 'Codice Fiscale') !!}
              {!! Form::text('cod_fiscale', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('tessera_sanitaria', 'Numero di tessera sanitaria') !!}
              {!! Form::text('tessera_sanitaria', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('consenso_affiliazione', 'Consenso all\'invio di comunicazioni') !!}
              <p>Prestando questo consenso, ci permetti di inviarti comunicazioni relative agli eventi organizzati dalla parrocchia.</p>
              {!! Form::hidden('consenso_affiliazione', 0) !!}
              {!! Form::checkbox('consenso_affiliazione', 1, null, ['class' => 'form-control']) !!}
            </div>
          </div>


          <h3>Luogo e data di nascita</h3>
          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('nato_il', 'Data di Nascita') !!}
              {!! Form::text('nato_il', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('id_nazione_nascita', 'Nazione di Nascita') !!}
              {!! Form::select('id_nazione_nascita', Nazione::pluck('nome_stato', 'id'), null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('id_provincia_nascita', 'Provincia di Nascita') !!}
              {!! Form::select('id_provincia_nascita', Provincia::pluck('nome', 'id'), null, ['class' => 'form-control', 'id' => 'id_provincia_nascita']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('id_comune_nascita', 'Comune di Nascita') !!}
              {!! Form::select('id_comune_nascita', Comune::where('id_provincia', Auth::user()->id_provincia_nascita)->pluck('nome', 'id'), null, ['class' => 'form-control', 'id' => 'id_comune_nascita']) !!}
            </div>
          </div>

          <h3>Residenza</h3>
          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('id_provincia_residenza', 'Provincia di residenza') !!}
              {!! Form::select('id_provincia_residenza', Provincia::pluck('nome', 'id'), null, ['class' => 'form-control', 'id' => 'id_provincia_residenza']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('id_comune_residenza', 'Comune di residenza') !!}
              {!! Form::select('id_comune_residenza', Comune::where('id_provincia', Auth::user()->id_provincia_residenza)->pluck('nome', 'id'), null, ['class' => 'form-control', 'id' => 'id_comune_residenza']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('via', 'Indirizzo') !!}
              {!! Form::text('via', null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <h3>Password</h3>
          <p>Inserisci qui la nuova password se vuoi cambiarla. Minimo 8 caratteri.</p>
          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('password_new', 'Nuova password') !!}
              {!! Form::password('password_new', ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col">
              {!! Form::label('confirm_password', 'Conferma password') !!}
              {!! Form::password('confirm_password', ['class' => 'form-control']) !!}
            </div>
          </div>

          <!--ATTRIBUTI//-->
          <?php
          $attributos = Attributo::where([['hidden', 0], ['id_oratorio', Session::get('session_oratorio')]])->orderBy('ordine', 'ASC')->get();
          ?>



          @if(count($attributos)>0)
          <h3>Informazioni aggiuntive</h3>
          @endif
          @foreach($attributos as $a)
          <?php
          $valore = AttributoUser::where([['id_user', $user->id],['id_attributo', $a->id]])->get();
          if(count($valore)>0){
            $attributo_val = $valore[0]->valore;
            $attributo_id = $valore[0]->id;
          }else{
            $attributo_val = null;
            $attributo_id = 0;
          }
          ?>
          <div class="col-lg-6">
            <div class="form-row">
              <div class="form-group col">
                {!! Form::hidden('id_attributo['.$loop->index.']', $a->id) !!}
                {!! Form::hidden('id_attributouser['.$loop->index.']', $attributo_id) !!}
                {!! Form::label('nome', $a->nome) !!}

                @if($a->id_type>0)
                {!! Form::select('valore['.$loop->index.']', TypeSelect::where('id_type', $a->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $attributo_val , ['class' => 'form-control'])!!}
                @else
                @if($a->id_type == -1)
                {!! Form::text('valore['.$loop->index.']', $attributo_val , ['class' => 'form-control']) !!}
                @elseif($a->id_type==-2)
                {!! Form::hidden('valore['.$loop->index.']', 0) !!}
                {!! Form::checkbox('valore['.$loop->index.']', 1, $attributo_val , ['class' => 'form-control']) !!}
                @elseif($a->id_type==-3)
                {!! Form::number('valore['.$loop->index.']', $attributo_val , ['class' => 'form-control']) !!}
                @endif
                @endif
              </div>
            </div>
          </div>
          @endforeach

          <div class="form-group">
            {!! Form::submit('Salva Profilo', ['class' => 'btn btn-primary form-control']) !!}
          </div>
          {!! Form::close() !!}

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function getListaComuni(id_provincia, field_name){
  $.ajax({
    type: 'GET',
    dataType: 'json',
    url: "{{route('comune.lista')}}",
    data: {
      id_provincia: id_provincia
    },
    success: function(response) {
      $('#'+field_name).empty();
      $.each(response, function(index, element) {
        $('#'+field_name).append($("<option></option>").attr("value",element.id).text(element.nome));
      });
    },
    error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
    async: false
  });
}

$('#id_provincia_nascita').change(function(){
  getListaComuni($('#id_provincia_nascita').val(), 'id_comune_nascita');
});

$('#id_provincia_residenza').change(function(){
  getListaComuni($('#id_provincia_residenza').val(), 'id_comune_residenza');
});
</script>
@endpush
