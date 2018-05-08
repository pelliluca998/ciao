<?php
use Modules\Event\Entities\Event;
use App\Classe;
use App\Squadra;
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')
<h1>Nuova Iscrizione</h1>
<p class="lead">Inserisci le informazioni per la nuova iscrizione qui sotto, oppure <a href="{{ route('events.index') }}">torna all'elenco completo.</a></p>
<hr>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-body">
			@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<p>{{ $error }}</p>
				@endforeach
			</div>
			@endif
			<?php
			$user = DB::table('users')->select(DB::raw("concat(users.name, ' ', users.cognome) as name"))->where('id', $id_user)->value('name');
			?>
			{!! Form::open(['route' => 'subscriptions.store']) !!}
				<div class="form-group">
				{!! Form::label('id_user', 'Utente') !!}
				{!! Form::label('id_user', $user  , ['class' => 'form-control']) !!}
				{!! Form::hidden('id_user', $id_user); !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_classe', 'Classe') !!}
				{!! Form::select('id_classe', Classe::where('id_oratorio', Session::get('session_oratorio'))->orderBy('ordine')->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('id_squadra', 'Squadra') !!}
				{!! Form::select('id_squadra', Squadra::where('id_event', Session::get('work_event'))->orderBy('nome')->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('confirmed', 'Confermata?') !!}
				{!! Form::checkbox('confirmed', null, true, ['class' => 'form-control']) !!}
				</div>			
				
				<div class="form-group">
				{!! Form::label('type', 'Tipo di iscrizione') !!}
				{!! Form::select('type', array('ADMIN' => 'Admin', 'WEB' => 'Web'), null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::submit('Salva Evento', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
