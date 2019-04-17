<?php
use Modules\Oratorio\Entities\Oratorio;

?>

@extends('layouts.app')

@section('content')

<div class="container">
  <h2 style="text-align: center">Seleziona l'oratorio a cui vuoi iscriverti</h2>
  <div class="row justify-content-center" style="margin-top: 20px;">

    <div class="col">
      <div class="card">
        <div class="card-body">

          <div class="card-deck">

            @foreach(Oratorio::orderBy('nome', 'ASC')->get() as $oratorio)
            <div class="card">

              <div class="card-img-top" style="height: 300px; background-color: #ADD8E6">
                @if($oratorio->logo == '' || $oratorio->logo == null)
                <h3 class="card-title" style="text-align: center; padding-top: 30px">{{ $oratorio->nome }}</h3>
                @else
                <img src="{!! url(Storage::url('public/'.$oratorio->logo)) !!}" style="height: 100%; width: 100%; object-fit: cover; " alt="">
                @endif
              </div>




              <div class="card-body">
                <h5 class="card-title" style="text-align: center">{{ $oratorio->nome }}</h5>
              </div>
              <div class="card-footer">
                {!! Form::open(['method' => 'GET', 'route' => ['register']]) !!}
                {!! Form::hidden('id_oratorio', $oratorio->id) !!}
                {!! Form::submit('Iscriviti a questo oratorio', ['class' => 'btn btn-primary form-control']) !!}
                {!! Form::close() !!}
              </div>

            </div>
            @if(!($loop->iteration % 4))
          </div>
          <div class="card-deck">
            @endif

            @endforeach
          </div>



        </div>
      </div>
    </div>

  </div>
</div>
@endsection
