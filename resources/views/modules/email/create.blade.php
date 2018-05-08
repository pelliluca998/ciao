<?php
use Modules\User\Entities\User;
use Modules\User\Entities\Group;
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
</script>
@extends('layouts.app')

@section('content')


{!! Form::open(['route' => 'email.send', 'files' => true]) !!}
<div class="container" >
	<div class="row">
		<h1>Invia Email ai contatti selezionati</h1>
		<p class="lead">oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
		<hr>
	</div>
    <div class="row" >
        <div class="">
		<?php $check_user=json_decode($check_user); 
				$users = User::whereIn('id', $check_user)->get();
				Session::reflash();
				?>
		<div class="panel panel-default panel-right">
			<div class="panel-heading">Contatti selezionati (<span id="user_count">{{count($check_user)}}</span>)</div>
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
		<div class="panel panel-default panel-left">
			<div class="panel-heading">Messaggio</div>
		<div class="panel-body">
			@if($errors->any())
			<div class="alert alert-danger">
				@foreach($errors->all() as $error)
					<p>{{ $error }}</p>
				@endforeach
			</div>
			@endif
			<?php
			//var_dump(json_decode($check_user));
			?>

				<div class="form-group">
				{!! Form::label('object', 'Oggetto') !!}
				{!! Form::text('object', null, ['class' => 'form-control']) !!}
				</div>
				
				<div class="form-group">
				{!! Form::label('message', 'Messaggio') !!}
				{!! Form::textarea('message', null, ['class' => 'form-control']) !!}
				</div>					
				
				<div class="form-group">
				{!! Form::label('attach', 'Allegato') !!}
				{!! Form::file('attach', null, ['class' => 'form-control']) !!}
				</div>
				
				{!! Form::hidden('id_users', Session::get('selected_users')) !!}
				{!! Form::hidden('id_groups', Session::get('selected_groups')) !!}
				{!! Form::hidden('id_event', Session::get('selected_sub')) !!}
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
