<?php
use App\LicenseType;
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')

<div class="container">
<div class="row">
<h1>Nuovo messaggio in homepage</h1>
<p class="lead">Inserisci il nuovo messaggio</p>
<hr>
</div>
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
			{!! Form::open(['route' => 'oratorio.save_message']) !!}
				<div class="form-group">
				{!! Form::label('title', 'Oggetto') !!}
				{!! Form::text('title', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('message', 'Messaggio') !!}
				{!! Form::textarea('message', null, ['class' => 'form-control']) !!}
				</div>	
				
            
                
				
				
				<div class="form-group">
				{!! Form::submit('Salva messaggio', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
