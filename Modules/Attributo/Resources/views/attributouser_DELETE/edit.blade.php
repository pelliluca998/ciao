<?php
use Modules\User\Entities\User;
use App\Type;
use Modules\User\Entities\Group;
use Modules\Attributo\Entities\Attributo;
use Modules\Attributo\Entities\AttributoUser;
use App\TypeSelect;
//use Session;
?>

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h1>Modifica Informazione utente</h1>
        <p class="lead">Modifica e salva l'informazione qui sotto, oppure <a href="{{ route('attributouser.show') }}?id_user={{$attributouser->id_user}}">torna all'elenco completo.</a></p>
        <hr>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
		<div class="panel-heading">Informazioni utente</div>
		<div class="panel-body">	
			{!! Form::model($attributouser, ['method' => 'PATCH','route' => ['attributouser.update', $attributouser->id]]) !!}
				{!! Form::hidden('id_attributouser', $attributouser->id) !!}
            <?php
            $attributo = Attributo::select('attributos.nome', 'attributos.id_type')
                ->leftJoin('types', 'types.id', 'attributos.id_type')
                ->where('attributos.id', $attributouser->id_attributo)
                ->first();
            ?>
				<div class="form-group">
				{!! Form::label('label', $attributo->nome) !!} 
                
		          @if($attributo->id_type>0)
					{!! Form::select('valore', TypeSelect::where('id_type', $attributo->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $attributouser->valore, ['class' => 'form-control'])!!}
				@else
					@if($attributo->id_type==-1)
						{!! Form::text('valore', $attributouser->valore, ['class' => 'form-control']) !!}
					@elseif($attributo->id_type==-2)
						{!! Form::hidden('valore', 0) !!}
						{!! Form::checkbox('valore', 1, $attributouser->valore, ['class' => 'form-control']) !!}
					@elseif($attributo->id_type==-3)
						{!! Form::number('valore', $attributouser->valore, ['class' => 'form-control']) !!}
					@elseif($attributo->id_type==-4)
						{!! Form::select('valore', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), $attributouser->valore, ['class' => 'form-control'])!!}				
					@endif
				@endif
				</div>

				<div class="form-group">
				{!! Form::submit('Salva Informazione', ['class' => 'btn btn-primary form-control']) !!}
				</div>
           		{!! Form::close() !!}           		
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
