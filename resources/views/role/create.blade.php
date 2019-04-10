@extends('layouts.app')

<?php
use Carbon\Carbon;
use App\Role;
use App\Permission;
$permissions = Permission::all();
?>

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class="fas fa-ruler-combined"></i> Ruoli e permessi</h1>
        <p class="lead">Aggiungi ruolo</p>
        <hr>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-4">
      <div class="card">
        <div class="card-body">
          @if($errors->any())
          <div class="alert alert-danger">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
          </div>
          @endif


          {!! Form::open(['method' => 'POST', 'route' => ['role.store']]) !!}
          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('display_name', 'Nome ruolo') !!}<br>
              {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col">
              {!! Form::label('description', 'Descrizione') !!}<br>
              {!! Form::text('description', null, ['class' => 'form-control']) !!}
            </div>
          </div>

          <div class="form-row" style="margin-top: 10px;">
            <div class="form-group col">
              {!! Form::submit('Salva', ['class' => 'btn btn-primary form-control']) !!}
            </div>
          </div>

          {!! Form::close() !!}



        </div>
      </div>
    </div>
  </div>
</div>
@endsection
