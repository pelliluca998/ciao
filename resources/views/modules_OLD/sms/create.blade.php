<?php
use App\User;
use App\Group;
?>
<script>
	function remove_user(user_id){
		//alert(user_id);
		$("#user_"+user_id).remove();
		$("#user_h_"+user_id).remove();
		$("#user_d_"+user_id).remove();
		$("#user_i_"+user_id).remove();
		$("#user_count").text($("[id^=user_h_]").length);
	}
	
	function conta_lettere(){
		$("#char").text($("#message").val().length);
	}
	
	function check_sms_type(type){
		if(type==106){
			$("#message").attr('maxlength','160');
			$("#message").val($("#message").val().substring(0,160));
		}else{
			$("#message").attr('maxlength','');
		}
		conta_lettere()
	}
</script>
@extends('layouts.app')

@section('content')


{!! Form::open(['route' => 'sms.send']) !!}
<div class="container">
	<div class="row">
		<h1>Invia Sms ai contatti selezionati</h1>
		<p class="lead">oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
		<hr>
	</div>
    <div class="row">
        <div class="">
<?php $check_user=json_decode($check_user); 
				$users = User::whereIn('id', $check_user)->get();
				Session::reflash();
				?>
		<div class="panel panel-default panel-left">
			<div class="panel-heading">Contatti selezionati (<span id="user_count">{{count($users)}}</span>)</div>
			<div class="panel-body">


				@foreach($users as $user)
					<i class="fa fa-user-o" id="user_i_{{$user->id}}"></i>
					{!! Form::label('user_'.$user->id, $user->cognome.' '.$user->name, ['id' => 'user_'.$user->id]) !!}
					{!! Form::hidden('user[]', $user->id, ['id' => 'user_h_'.$user->id]) !!} 
					<i class="fa fa-trash" onclick="remove_user({{$user->id}});" id="user_d_{{$user->id}}"></i>
					<br>
				@endforeach
				</div>

		</div>
		<div class="panel panel-default panel-right">
			<div class="panel-heading">Messaggio</div>
		<div class="panel-body">
			@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<p>{{ $error }}</p>
				@endforeach
			</div>
			@endif

				<div class="form-group">
				{!! Form::radio('sms_type', 106, 0, ['onclick' => 'check_sms_type(106)']) !!} {!! Form::label('sms_type', 'SMS Smart (1 credito)') !!} 
				{!! Form::radio('sms_type', 84, 1, ['onclick' => 'check_sms_type(84)']) !!} {!! Form::label('sms_type', 'SMS Pro (1.6 crediti)') !!}
				</div>

				<div class="form-group">
				{!! Form::label('message', 'Testo SMS') !!}
				{!! Form::text('message', null, ['id' => 'message', 'oninput' => 'conta_lettere()', 'class' => 'form-control']) !!}<br>
				Lunghezza: <span id="char">0</span> caratteri.
				</div>			
				
				
				<div class="form-group">
				{!! Form::submit('Invia!', ['class' => 'btn btn-primary form-control']) !!}
				</div>				
				

                   
                </div>
            </div>
        </div>
    </div>
</div>
	{!! Form::close() !!}
	

@endsection
