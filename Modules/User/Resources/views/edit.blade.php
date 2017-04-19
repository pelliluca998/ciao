<?php
use App\User;
use App\Role;
use App\RoleUser;
?>

@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<h1>Modifica utente</h1>
		<p class="lead">Modifica e salva l'utente qui sotto, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
		<hr>	
	</div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-heading">Anagrafica Utenti</div>
		<div class="panel-body">
			<?php
			$role = RoleUser::where([['user_id', $user->id]])->first();
			if($role==null || count($role)==0){
                $role2 = Role::where([['name', '=', 'user'],['id_oratorio', Session::get('session_oratorio')]])->first();
                $user->id_role = $role2->id;
            }else{
                $user->id_role = $role->role_id;
            }
			?>
			{!! Form::model($user, ['method' => 'PATCH','files' => true,'route' => ['user.update', $user->id]]) !!}
				{!! Form::hidden('id_user', $user->id) !!}
				<div class="form-group">
				{!! Form::label('name', 'Nome') !!}
				{!! Form::text('name', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('cognome', 'Cognome') !!}
				{!! Form::text('cognome', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('sesso', 'Sesso') !!}
				{!! Form::select('sesso', array('M' => 'Uomo', 'F' => 'Donna'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('nato_il', 'Data di Nascita') !!}
				{!! Form::text('nato_il', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('nato_a', 'Luogo di Nascita') !!}
				{!! Form::text('nato_a', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('residente', 'Residente a') !!}
				{!! Form::text('residente', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('via', 'Indirizzo') !!}
				{!! Form::text('via', null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::label('email', 'Email') !!}
				{!! Form::text('email', null, ['class' => 'form-control']) !!}
				</div>
			
				<div class="form-group">
				{!! Form::label('cell_number', 'Numero cellulare') !!}
				{!! Form::text('cell_number', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('username', 'Username') !!}
				{!! Form::text('username', null, ['class' => 'form-control']) !!}
				</div>
			
				<div class="form-group">
					<div class="form-group" style="width: 48%; float: left;">
						{!! Form::label('photo', 'Foto Profilo') !!}
						{!! Form::file('photo', null, ['class' => 'form-control']) !!}
					</div>

					<div class="form-group" style="width: 48%; float: left;">
						Foto attuale:<br>
						<?php
						if($user->photo!=''){
							echo "<img src='".url(Storage::url('public/'.$user->photo))."' width=200px/>";
						}else{
							echo "Nessuna immagine!<br><br>";
						}
						?>
					</div>
				</div>
			
				<div class="form-group">
				{!! Form::label('id_role', 'Ruolo') !!}
				{!! Form::select('id_role', Role::where('id_oratorio', Session::get('session_oratorio'))->orderBy('id')->pluck('display_name', 'id'), null, ['class' => 'form-control']) !!}
				</div>

				<div class="form-group">
				{!! Form::submit('Salva Utente', ['class' => 'btn btn-primary form-control']) !!}
				</div>
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
