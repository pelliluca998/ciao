<?php
use Modules\User\Entities\User;
use App\Role;
use App\Permission;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use Modules\User\Entities\Group;
use Modules\Event\Entities\Week;

use App\SpecSubscription;
use App\TypeSelect;
use Modules\Subscription\Entities\Subscription;

?>
<html lang="en">
<head>    
</head>

<!-- Modal2 -->
<div class="modal fade" id="eventspecsOp" tabindex="-1" role="dialog" aria-labelledby="EventSpecsOperation">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Aggiungi Specifica</h4>
				<p>1) Scegli se vuoi inserire la specifica...</p>
				<?php
					$options = "<option value='0'>Generale</option>";
					$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $id_event)->orderBy('from_date', 'asc')->get();
					foreach($weeks as $w){
						$options .= "<option value=".$w->id.">Settimana dal ".$w->from_date." al ".$w->to_date."</option>";
					}
				?>
				<select id="valid_for" onchange="change_eventspec(this, {{$id_event}})" class="form-control"><?php echo $options; ?></select>
				<p>2) Quale specifica?</p>
				<select id="event_spec" class="form-control"></select><br>
				
				<i onclick="add_eventspec({{$id_subscription}}, {{$id_event}}, false)" class="btn btn-primary" style="width: 45%"><i class="fa fa-plus" aria-hidden="true"></i>Inserisci</i>
			</div>         

			<div class="modal-body">				

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>


<?php
//carico tutte le settimane
$specs = (new EventSpecValue)->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_specs.id_type as id_type', 'event_spec_values.valore', 'event_spec_values.id')
	->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
	->where([['event_spec_values.id_subscription', $id_subscription], ['event_specs.hidden', 0], ['event_specs.general', 1]])
	->orderBy('event_spec_values.id_eventspec', 'asc')
	->get();

$subscription = Subscription::findOrFail($id_subscription);
$index=0;
?>
{!! Form::open(['route' => 'eventspecvalues.save']) !!}

@if(count($specs)>0)
	<h2>Specifiche generali</h2>
	<table class='testgrid' id='showeventspecvalue'>
		<thead><tr>
		<th style='width: 35%;'>Specifica</th>
		<th>Valore</th>
		<th>Costo (€)</th>
		<th>Pagato</th>
		<th></th>
		</tr></thead>
		@foreach($specs as $spec)
			<tr>
			@if($subscription->confirmed==0)
				<input type="hidden" name="id_eventspecvalue[{{$loop->index}}]" value="{{$spec->id}}"/>
				<input type="hidden" name="id_eventspec[{{$loop->index}}]" value="{{$spec->id_eventspec}}"/>
				<input type="hidden" name="id_subscription[{{$loop->index}}]" value="{{$id_subscription}}"/>
				<td>{{$spec->label}}</td>
				<td>
				@if($spec->id_type>0)
					{!! Form::select('valore['.$loop->index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $spec->valore, ['class' => 'form-control'])!!}
				@else
					@if($spec->id_type==-1)
						{!! Form::text('valore['.$loop->index.']', $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-2)
						{!! Form::hidden('valore['.$loop->index.']', 0) !!}
				     	{!! Form::checkbox('valore['.$loop->index.']', 1, $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-3)
						{!! Form::number('valore['.$loop->index.']', $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-4)
						{!! Form::select('valore['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), $spec->valore, ['class' => 'form-control'])!!}				
					@endif
				@endif
				</td>
				<td>
					{{$spec->costo}}€
					{!! Form::hidden('costo['.$loop->index.']', $spec->costo) !!}
				</td>
				<td>
					@if($spec->pagato==1)
						<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
						{!! Form::hidden('pagato['.$loop->index.']', 1) !!}
					@else
						<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
						{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
					@endif
					
				</td>
				<td><a href="{{url('eventspecvalues', [$spec->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
			@else
				<td>{{$spec->label}}</td>
				<td>
				@if($spec->id_type>0)
					{!! Form::label($loop->index, TypeSelect::where('id', $spec->valore)->first()->option)!!}
				@else
					@if($spec->id_type==-1)
						{!! Form::label($loop->index, $spec->valore) !!}
					@elseif($spec->id_type==-2)
				     	@if($spec->valore==1)
				     		<i class="fa fa-check-square-o fa-2x" aria-hidden='true'></i>
				     	@else
				     		<i class="fa fa-square-o fa-2x" aria-hidden='true'></i>
				     	@endif
					@elseif($spec->id_type==-3)
						{!! Form::label($loop->index, $spec->valore) !!}
					@elseif($spec->id_type==-4)
						{!! Form::label($loop->index, Group::where('id', $spec->valore)->first()->nome)!!}				
					@endif
				@endif
				</td>
				<td>
					{{$spec->costo}}€
					{!! Form::hidden('costo['.$loop->index.']', $spec->costo) !!}
				</td>
				<td>
					@if($spec->pagato==1)
						<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
						{!! Form::hidden('pagato['.$loop->index.']', 1) !!}
					@else
						<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
						{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
					@endif
					
				</td>
				<td></td>
			@endif
			</tr>
			@php
				$index=$loop->index+1
			@endphp
		@endforeach
	</table>
@else
	<i>Nessuna specifica generale!</i>
@endif


<?php
$weeks = (new Week)->select('id', 'from_date', 'to_date')->where('id_event', $id_event)->orderBy('from_date', 'asc')->get();
?>
@if(count($weeks)>0)
<h2>Specifiche settimanali</h2>

@foreach($weeks as $w)
	<?php
		$specs = (new EventSpecValue)
			->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_specs.id_type as id_type', 'event_specs.valid_for', 'event_spec_values.valore', 'event_spec_values.id', 'event_spec_values.costo', 'event_spec_values.pagato')
			->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
			->where([['event_spec_values.id_subscription', $id_subscription], ['event_specs.general', 0], ['event_spec_values.id_week', $w->id]])
			->orderBy('event_spec_values.id_eventspec', 'asc')
			->get();
	?>
	
	<p><b>Settimana {{$loop->index+1}} - dal {{$w->from_date}} al {{$w->to_date}}</b></p>
	<table class='testgrid' id="weektable_{{$w->id}}">
	<thead><tr>
	<th style='width: 35%;'>Specifica</th>
	<th>Valore</th>
	<th>Costo (€)</th>
	<th>Pagato</th>
	<th></th>
	</tr></thead>
	
	@foreach($specs as $spec)
		<?php
		$valid = json_decode($spec->valid_for, true);
		?>
		@if($valid[$w->id]==1)		
			<tr>
				<input type="hidden" name="id_eventspecvalue[{{$index}}]" value="{{$spec->id}}" />
				<input type="hidden" name="id_eventspec[{{$index}}]" value="{{$spec->id_eventspec}}" />
				<input type="hidden" name="id_subscription[{{$index}}]" value="{{$id_subscription}}" />
				<td>{{$spec->label}}</td>
				<td>
				@if($spec->id_type>0)
					{!! Form::select('valore['.$index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $spec->valore, ['class' => 'form-control'])!!}
				@else
					@if($spec->id_type==-1)
						{!! Form::text('valore['.$index.']', $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-2)
						{!! Form::hidden('valore['.$index.']', 0) !!}
		               	{!! Form::checkbox('valore['.$index.']', 1, $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-3)
						{!! Form::number('valore['.$index.']', $spec->valore, ['class' => 'form-control']) !!}
					@elseif($spec->id_type==-4)
						{!! Form::select('valore['.$index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), $spec->valore, ['class' => 'form-control'])!!}				
					@endif
				@endif
				</td>
				<td>
					{{$spec->costo}}€
					{!! Form::hidden('costo['.$index.']', $spec->costo) !!}
				</td>
				<td>
					@if($spec->pagato==1)
						<i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
					@else
						<i class="fa fa-square-o fa-2x" aria-hidden="true"></i>
					@endif
					{!! Form::hidden('pagato['.$index.']', $spec->pagato) !!}
				</td>
				<td><a href="{{url('eventspecvalues', [$spec->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
			</tr>
		
		@php
			$index++
		@endphp
		@endif
	@endforeach
	</table><br>
@endforeach
@else
	<i>Nessuna settimana inserita!</i>
@endif




@if($subscription->confirmed==0)
	<input id='contatore_e' type='hidden' value="{{$index}}" />
	<input id='id_event' type='hidden' value='0' />
	{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 45%']) !!}
		<button style="font-size: 15px; width: 49%;" type='button' class="btn btn-primary btn-sm" data-toggle='modal' data-target='#eventspecsOp' data-name='' data-eventid=''><i class="fa fa-plus" aria-hidden="true"></i> <i>Aggiungi specifica</i></button>
	{!! Form::close() !!}
@endif


<script>
$(document).ready(function(){
	$('#eventspecsOp').on('show.bs.modal', function (event) {
	$('#valid_for').trigger("change");
	var button = $(event.relatedTarget) // Button that triggered the modal
	var name = button.data('name') // Extract info from data-* attributes
	var eventid = button.data('eventid');
	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	var modal = $(this);
	modal.find('#name').text(name);
	modal.find("[id*='id_event']").val(eventid);
	});
});
</script>		

</html>
