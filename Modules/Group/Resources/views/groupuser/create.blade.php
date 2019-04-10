<?php
use Modules\Group\Entities\User;
use Modules\Group\Entities\Group;
?>

@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-users'></i> Gruppi utenti</h1>
        <p class="lead">Seleziona il gruppo a cui assegnare gli utenti selezionati, oppure <a href="{{ route('user.index') }}">torna all'anagrafica completa.</a></p>
        <hr>
      </div>
    </div>
  </div>

  <div class="row justify-content-center" style="margin-top: 20px;">
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          @if($errors->any())
          <div class="alert alert-danger">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
          </div>
          @endif

          {!! Form::open(['route' => 'groupusers.store_user']) !!}
          <div class="form-group">
            {!! Form::label('id_gruppo', 'Gruppo') !!}
            {!! Form::select('id_gruppo', Group::where('id_oratorio', Session::get('session_oratorio'))->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}
          </div>

          {!! Form::hidden('check_user', $check_user) !!}
          <div class="form-group">
            {!! Form::submit('Assegna', ['class' => 'btn btn-primary form-control']) !!}
          </div>

          {!! Form::close() !!}

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
