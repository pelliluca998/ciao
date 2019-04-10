<?php
use Modules\Event\Entities\Event;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card bg-transparent border-0">
        <h1><i class='fas fa-calendar-alt'></i> {{ $event->nome }}</h1>
        <p class="lead">Dettagli dell'evento</p>
        <hr>
      </div>
    </div>
  </div>

  <div class="row justify-content-center" style="margin-top: 20px;">
    @if($event->image != '' && $event->image != null)
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">

          <img src="{!! url(Storage::url('public/'.$event->image)) !!}" style="height: 100%; width: 100%; object-fit: cover; " alt="">

        </div>
      </div>
    </div>
    @endif

    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">

          {!! $event->descrizione !!}

          <div class="card-footer">
            {!! Form::open(['route' => 'subscribe.create']) !!}
            {!! Form::hidden('id_event', $event->id); !!}
            {!! Form::hidden('id_user', Auth::user()->id); !!}
            {!! Form::submit('Iscriviti', ['class' => 'btn btn-primary form-control']) !!}
            {!! Form::close() !!}
          </div>


        </div>
      </div>
    </div>


  </div>
</div>


@endsection

@push('scripts')
<script>
$(document).ready(function(){

});
</script>
@endpush
