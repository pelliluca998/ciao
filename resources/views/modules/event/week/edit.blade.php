<?php
use Modules\User\Entities\User;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <h1><i class="fas fa-sun" aria-hidden="true"></i> Settimane</h1>
    <p class="lead">Modifica le date di inizio e fine per la settimana</p>
    <hr>
  </div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2" style="margin-left: 5%; width: 90%;">
      <div class="panel panel-default">
        <div class="panel-body">
          @if($errors->any())
          <div class="alert alert-danger">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
          </div>
          @endif

          {!! Form::model($week, ['method' => 'PATCH','route' => ['week.update', $week->id]]) !!}
					{!! Form::hidden('id_week', $week->id) !!}
          <div class="form-group">
            <div class="form-group panel-left">
              {!! Form::label('from_date', 'Data inizio') !!}
              {!! Form::text('from_date', null, ['class' => 'form-control data']) !!}
            </div>

            <div class="form-group panel-right">
              {!! Form::label('to_date', 'Data fine') !!}
              {!! Form::text('to_date', null, ['class' => 'form-control data']) !!}
            </div>
          </div>


          <div class="form-group">
            {!! Form::submit('Salva settimana', ['class' => 'btn btn-primary form-control']) !!}
          </div>

          {!! Form::close() !!}


        </div>
      </div>
    </div>
  </div>
</div>
@endsection
