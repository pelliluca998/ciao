<?php

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
			<p>Telegram è una fantastica app per Android/iOS che facilita la comunicazione tra amici. Sicuramente ne avrai già sentito parlare! Da oggi puoi fare in modo che il tuo don o l'amministratore del tuo oratorio possano contattarti attraverso Telegram relativamente agli eventi a cui sei iscritto. Questo semplifica e velocizza sicuramente la comunicazione! <br><br>Per poter utilizzare Telegram, devi prima fare in modo che Telegram ti riconosca. <br>Per fare ciò, puoi aprire direttamente Telegram cliccando sul pulsante qui sotto, oppure farti mandare un SMS sul tuo smartphone, con il link per eseguire il login.</p>
			<a href='{!! route('telegram.login') !!}' class="btn btn-primary">Apri Telegram</a> <a href='{!! route('telegram.sms_login') !!}' class="btn btn-primary">Invia SMS</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

?>
@endsection
