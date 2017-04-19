<?php
use App\Week;
use App\Event;
use App\SpecSubscription;
use App\User;
use App\CampoWeek;
use App\Oratorio;
use App\LicenseType;
use App\UserOratorio;
?>

@extends('layouts.app')
@section('content')
<div class="container">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Benvenuto!</div>


				<div class="panel-body">
					<p>Inserisci qui sotto le informazioni richieste e clicca su Invia. Verrai contattato a breve!</p>
					{!! Form::open(['route' => 'oratorio.neworatorio_emailfromuser']) !!}
						<div class="form-group">
							{!! Form::label('nome', 'Nome oratorio') !!}
							{!! Form::text('nome', null, ['class' => 'form-control']) !!}
						</div>
						
						<div class="form-group">
							{!! Form::label('email', 'Email') !!}
							{!! Form::text('email', null, ['class' => 'form-control']) !!}
						</div>
						
						<div class="form-group">
							{!! Form::submit('Invia!', ['class' => 'btn btn-primary form-control']) !!}
						</div>
					{!! Form::close() !!}
				</div>


            </div>
        </div>
    </div>
</div>
@endsection
