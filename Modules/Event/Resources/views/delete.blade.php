<?php
use App\Event;
?>

@extends('layouts.app')

@section('content')
<h1>{{ $event->nome }}</h1>
<hr>

<div class="row">
    <div class="col-md-6">
        <a href="{{ route('tasks.index') }}" class="btn btn-info">Torna all'anagrafica</a>
    </div>
    <div class="col-md-6 text-right">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['users.destroy', $user->id]
        ]) !!}
            {!! Form::submit('Vuoi eliminare questo utente?', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </div>
</div>

@stop
