<?php
use App\TelegramUser;
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
		<div class="panel-heading">Telegram</div>
		<div class="panel-body">
			<?php
			$user = TelegramUser::leftJoin('user_oratorio', 'user_oratorio.id_user', 'telegram_user.id_user')
			->where('user_oratorio.id_oratorio', Session::get('session_oratorio'))
			->get();
			
               
               ?>
               Attualmente hai {{count($user)}} utenti che utilizzano Telegram.
                </div>
            </div>
        </div>
    </div>
</div>

<?php

?>
@endsection
