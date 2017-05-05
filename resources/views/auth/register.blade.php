<?php
use App\Oratorio;
?>

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Registrazione a Segresta</div>
                <div class="panel-body">
					@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif

				<?php
				$oratorio = null;
                      if(isset($_GET['id_oratorio'])){
                      	$oratorio = Oratorio::where('reg_token', $_GET['id_oratorio'])->first();
                      	if ($oratorio!=null){
                      		echo "<div style='text-align:center;'>";
                      		if($oratorio->logo!=''){
							echo "<img src='".url(Storage::url('public/'.$oratorio->logo))."' width='170px' ><br>";
						}
                      		echo "<h2>".$oratorio->nome."</h2>";
                      		echo "</div>";
                      	}
                      }
                      
                      ?>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}
                        
                        
                        
                        
                        
                        
                        

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nome</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('cognome') ? ' has-error' : '' }}">
                            <label for="cognome" class="col-md-4 control-label">Cognome</label>

                            <div class="col-md-6">
                                <input id="cognome" type="text" class="form-control" name="cognome" value="{{ old('cognome') }}" required autofocus>

                                @if ($errors->has('cognome'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cognome') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('nato_il') ? ' has-error' : '' }}">
                            <label for="nato_il" class="col-md-4 control-label">Nato il</label>

                            <div class="col-md-6">
                                <input id="nato_il" type="text" class="form-control" name="nato_il" value="{{ old('nato_il') }}" required autofocus>

                                @if ($errors->has('nato_il'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nato_il') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('nato_a') ? ' has-error' : '' }}">
                            <label for="nato_a" class="col-md-4 control-label">Nato a</label>

                            <div class="col-md-6">
                                <input id="nato_a" type="text" class="form-control" name="nato_a" value="{{ old('nato_a') }}" required autofocus>

                                @if ($errors->has('nato_a'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nato_a') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('residente') ? ' has-error' : '' }}">
                            <label for="residente" class="col-md-4 control-label">Residenza</label>

                            <div class="col-md-6">
                                <input id="residente" type="text" class="form-control" name="residente" value="{{ old('residente') }}" required autofocus>

                                @if ($errors->has('residente'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('residente') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('via') ? ' has-error' : '' }}">
                            <label for="via" class="col-md-4 control-label">Indirizzo</label>

                            <div class="col-md-6">
                                <input id="via" type="text" class="form-control" name="via" value="{{ old('via') }}" required autofocus>

                                @if ($errors->has('via'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('via') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('sesso') ? ' has-error' : '' }}">
                            <label for="sesso" class="col-md-4 control-label">Sesso</label>

                            <div class="col-md-6">
                                <select id="sesso" class="form-control" name="sesso"><option value="M">Uomo</option><option value="F">Donna</option></select>
                                @if ($errors->has('sesso'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sesso') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('id_oratorio') ? ' has-error' : '' }}">
                            <label for="id_oratorio" class="col-md-4 control-label">Oratorio</label>

                            <div class="col-md-6">
                                <?php
                                if ($oratorio==null){
                                	$oratorio = Oratorio::where('reg_visible', 1)->get();
                                	echo "<select id='id_oratorio' class='form-control' name='id_oratorio' onchange='load_attrib_registration(this)'>";
                                	echo "<option value='0'>Seleziona oratorio</option>";
                                	echo "<option value='-1'>Nuovo Oratorio</option>";
                                	foreach($oratorio as $o){
                                		echo "<option value='".$o->id."'>".$o->nome."</option>";
                                	}
                                	echo "</select>";
                                }else{
                                	//echo $oratorio->nome;
                                	echo "<select id='id_oratorio' class='form-control' name='id_oratorio' onchange='load_attrib_registration(this)'>";
                                	echo "<option value='".$oratorio->id."'>".$oratorio->nome."</option>";
                                	//echo "<input type='hidden' name='id_oratorio' id='id_oratorio' value='".$oratorio->id."' />";
                                	echo "</select>";
                                	
                                }
                                ?>                                
                                
                                @if ($errors->has('id_oratorio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_oratorio') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('usernam$(document).ready(function(){e') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 
						
						<div class="form-group{{ $errors->has('cell_number') ? ' has-error' : '' }}">
                            <label for="cell_number" class="col-md-4 control-label">Cellulare</label>

                            <div class="col-md-6">
                                <input id="cell_number" type="text" class="form-control" name="cell_number" value="{{ old('cell_number') }}" required>

                                @if ($errors->has('cell_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cell_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Conferma Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <span id="attributes">
                        </span>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Registrati
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$("#id_oratorio").trigger("change");
});
</script>
@endsection
