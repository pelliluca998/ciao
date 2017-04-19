<?php
use App\Oratorio;
use App\UserOratorio;
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
                <div class="panel-heading">Affiliazione</div>


                <div class="panel-body">
                	<p>Scegli dalla lista qui sotto un oratorio a cui affiliarti</p>
				{!! Form::open(['route' => 'oratorio.salva_affiliazione']) !!}
					{!! Form::select("id_oratorio", Oratorio::where('reg_visible', true)->pluck('nome', 'id'), null, ['class' => 'form-control']) !!}<br>
					{!! Form::submit('Salva!', ['class' => 'btn btn-primary form-control']) !!}
				{!! Form::close() !!}
				
				<div style="margin-top: 50px;">
					<?php
						$userOratorio = UserOratorio::select('user_oratorio.id', 'oratorios.nome')
						->leftJoin('oratorios', 'oratorios.id', 'user_oratorio.id_oratorio')
						->where('id_user', Auth::user()->id)
						->get();
					?>
					@if(count($userOratorio)>0)
						<p>Nella tabella qui sotto trovi la lista di oratori a cui sei attualmente affiliato. Clicca sul cestino per togliere l'affiliazione.</p>
						<table class="testgrid">
						<tr>
							<th>Oratorio</th>
							<th>Elimina</th>
						</tr>
						@foreach($userOratorio as $user)
							<tr>
								<td>{{$user->nome}}</td>
								<td>
								{!! Form::open(['route' => 'oratorio.elimina_affiliazione']) !!}
									{!! Form::hidden('id_useroratorio', $user->id) !!}
									{!! Form::submit('Elimina', ['class' => 'btn btn-primary form-control']) !!}
								{!! Form::close() !!}
								</td>
							</tr>
						@endforeach
						</table>
					@endif
					
				</div>


            </div>
        </div>
    </div>
</div>
@endsection
