<?php
use Modules\Subscription\Entities\Subscription;
use App\Role;
use App\Permission;
?>

@extends('layouts.app')

@section('content')
<div class="container" style="margin-left: 0px; margin-right: 0px; width: 100%;">
@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="">
		<div class="panel panel-default panel-left">
		<div class="panel-heading">Le tue iscrizioni</div>
		<div class="panel-body">
			<?php
			$query = Input::query(); //sono i parametri GET della tabella
			Session::put('query_param', $query);
			if(!isset($id_event) || $id_event==null){
				$id_event=Session::get('work_event');
			}
			$query = (new Subscription)
			    ->select('subscriptions.id', 'subscriptions.confirmed', 'events.nome', 'events.id as id_event')
			    ->leftJoin('events', 'events.id', '=', 'subscriptions.id_event')
			    ->where([['subscriptions.id_user', '=', Auth::user()->id],['events.active', 1]])->get();
			?>
			<table class='testgrid'>
				<thead><tr>
				<th>ID</th>
				<th>Evento</th>
				<th>Approvata?</th>
				<th>Cancella</th>
				<th>Stampa</th>
				<th>Dettagli</th>
				</tr></thead>
				@foreach($query as $sub)
					<tr>
					<td>{{$sub->id}}</td>
					<td>{{$sub->nome}}</td>
					@if($sub->confirmed==1)
						<td><i class="far fa-check-circle fa-2x" aria-hidden='true'></i></td>
						<td><i class="fas fa-trash fa-2x" aria-hidden="true" style="cursor: unset;"></i></td>
					@else
						<td><i class="far fa-circle fa-2x" aria-hidden='true'></i></td>
						<td><a href="{{route('subscription.destroy', ['id_sub' => $sub->id])}}"><i class="fas fa-trash fa-2x" aria-hidden="true" style="cursor: unset;"></i></a></td>
					@endif
					<td><a href="{{route('subscription.print', ['id_subscription' => $sub->id])}}"><i class="fa fa-print fa-2x" aria-hidden='true'></i></a></td>
					<td><i style="color:#3e93c3" onclick="load_spec_usersubscription({{$sub->id}}, {{$sub->id_event}})" class="fa fa-flag fa-2x" aria-hidden="true"></i></td>
					</tr>
				@endforeach
			</table>

                </div>
            </div>

		<div class="panel-right">
		<div class="panel panel-default">
			<div class="panel-heading">Dettagli dell'iscrizione - Parte 1</div>
			<div id="spec1" class="panel-body">

			</div>
		</div>
		</div>
    </div>
</div>


<?php

?>
@endsection
