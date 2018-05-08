<?php
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')
<?php
$users = User::whereIn('id', json_decode($check_user))->get();
?>

<div class="container">
	<div class="row">
		<h1><i class="fab fa-whatsapp"></i> Nuovo messaggio WhatsApp</h1>
		<p class="lead"></p>
		<hr>
	</div>
	<div class="row">
		{!! Form::open(['route' => 'whatsapp.send', 'files' => true]) !!}
		<div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
			<div class="panel panel-default panel-left">
				<div class="panel-heading">Elenco dei contatti (<span id="user_count">{{count($users)}}</span>)</div>
				<div class="panel-body">

					@foreach($users as $user)
					<i class="fas fa-user" id="user_i_{{$user->id}}"></i>
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
						{!! Form::label('message', 'Testo Messaggio') !!}
						{!! Form::text('message', null, ['id' => 'message', 'class' => 'form-control']) !!}<br>
					</div>

					<div class="form-group">
					{!! Form::label('attach', 'Allegato (Immagine o documento)') !!}
					{!! Form::file('attach', null, ['class' => 'form-control']) !!}
					</div>


					<div class="form-group">
						{!! Form::submit('Invia!', ['class' => 'btn btn-primary form-control']) !!}
					</div>
				</div>
			</div>

		</div>
		{!! Form::close() !!}
	</div>
</div>

@endsection

@push('scripts')
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
@endpush
