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
        <p class="lead">Imposta i permessi per ciascun ruolo oppure crea un nuovo ruolo</p>
        <hr>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col">
      <div class="card">
        <div class="card-body" style="overflow-x: auto; margin-right: 10px;">
          @if(Session::has('flash_message'))
          <div class="alert alert-success">
            {{ Session::get('flash_message') }}
          </div>
          @endif

          <a href="{{ route('role.create') }}" class="btn btn-m btn-primary editor_create"><i class="fa fa-plus"></i> Aggiungi ruolo</a>

          {!! Form::open(['method' => 'POST', 'route' => ['role.updatePermission']]) !!}
          <table class="table table-bordered" style="width: 100%;">
            <thead>
              <tr>
                <th rowspan="2" style="vertical-align: middle; text-align: center">Ruolo</th>
                <th colspan="{{ count($permissions) }}" style="text-align: center">Permessi</th>
              </tr>
              <tr>
                @foreach($permissions as $permission)
                <th>{{ $permission->display_name }}</th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach(Role::where('id_oratorio', Session::get('session_oratorio'))->get() as $role)
              <tr>
                <td><b>{{ $role->display_name }}</b><br>{{ $role->description }} - <a href="{{ route('role.delete', ['id_role' => $role->id]) }}">Elimina</a></td>
                @foreach($permissions as $permission)
                  <td>{!! Form::checkbox('permesso['.$role->id.'][]', $permission->id, $role->hasPermission($permission->name), ['class' => 'form-control']) !!}</td>
                @endforeach
              </tr>
              @endforeach
            </tbody>

          </table>

          <div class="form-row" style="margin-top: 10px;">
            <div class="form-group col">
            {!! Form::submit('Salva', ['class' => 'btn btn-primary form-control']) !!}
          </div>

          {!! Form::close() !!}



        </div>
      </div>
    </div>
  </div>
</div>
@endsection
