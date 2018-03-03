<?php
use Modules\User\Entities\User;
use Modules\Event\Entities\Week;
use App\Role;
use Modules\User\Entities\Group;
use App\Permission;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use App\SpecSubscription;
use Modules\Subscription\Entities\Subscription;
use Modules\Event\Entities\Event;
use App\TypeSelect;
use Modules\Oratorio\Http\Controllers\TypeController;

?>
<html lang="en">
<head>

</head>
<?php
$id_event=Session::get('work_event');
$id_subscription=$id_sub;
$subscription = Subscription::findOrFail($id_subscription);
$event = Event::findOrfail($id_event);
?>
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

				<i onclick="add_eventspec({{$id_subscription}}, {{$id_event}}, true)" class="btn btn-primary" style="width: 45%"><i class="fa fa-plus" aria-hidden="true"></i>Inserisci</i>
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
echo Form::open(['route' => 'eventspecvalues.save']);
$index=0;
//carico tutte le specifiche generali (general=1)
$specs = (new EventSpecValue)
	->select('event_spec_values.id_eventspec', 'event_specs.label', 'event_specs.id_type as id_type', 'event_spec_values.valore', 'event_spec_values.id', 'event_spec_values.costo', 'event_spec_values.pagato')
	->leftJoin('event_specs', 'event_specs.id', '=', 'event_spec_values.id_eventspec')
	->where([['event_spec_values.id_subscription', $id_subscription], ['event_specs.general', 1]])
	->orderBy('event_specs.ordine', 'asc')
	->get();
?>

<div style="padding: 2px; text-align: center; background: #90EE90;" id="nome_sub">
	@if($event->stampa_anagrafica)
		<h2>{{User::findOrfail($subscription->id_user)->full_name}}</h2>
	@else
		<?php
			$query = Subscription::select('event_spec_values.valore as valore')
					->leftJoin('event_spec_values', function ($join) use ($event){
				  		$join->on('subscriptions.id', '=', 'event_spec_values.id_subscription')
				      		->where('event_spec_values.id_eventspec', '=', $event->spec_iscrizione);
			  		})
			  		->where('subscriptions.id', $id_subscription)
			  		->first();
		?>
		<h2>{{$query->valore}}</h2>
	@endif
</div>


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
			<input type="hidden" name="id_eventspecvalue[{{$loop->index}}]" value="{{$spec->id}}" />
			<input type="hidden" name="id_eventspec[{{$loop->index}}]" value="{{$spec->id_eventspec}}" />
			<input type="hidden" name="id_subscription[{{$loop->index}}]" value="{{$id_subscription}}" />
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
					<?php
					$gruppi = Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->get();
					?>
					@if(count($gruppi)>0)
						{!! Form::select('valore['.$loop->index.']', Group::where('id_oratorio', Session::get('session_oratorio'))->orderBy('nome', 'ASC')->pluck('nome', 'id'), $spec->valore, ['class' => 'form-control']) !!}
					@else
						<i style="font-size: 12px;">Nessun gruppo disponibile!</i>{!! Form::hidden('valore['.$loop->index.']', 0) !!}
					@endif

				@endif
			@endif
			</td>
			<td>
				{!! Form::number('costo['.$loop->index.']', $spec->costo, ['class' => 'form-control', 'style' => 'width: 70px;', 'step' => '0.01']) !!}
			</td>
			<td>
				{!! Form::hidden('pagato['.$loop->index.']', 0) !!}
				@if($spec->costo>0)
             {!! Form::checkbox('pagato['.$loop->index.']', 1, $spec->pagato, ['class' => 'form-control']) !!}
        @endif
			</td>
			<td><a href="{{url('eventspecvalues', [$spec->id])}}/destroy"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a></td>
		</tr>
		@php
			$index=$loop->index+1
		@endphp
	@endforeach
</table><br>


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
			->orderBy('event_specs.ordine', 'asc')
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
					{!! Form::number('costo['.$index.']', $spec->costo, ['class' => 'form-control', 'style' => 'width: 70px;', 'step' => '0.01']) !!}
				</td>
				<td>
					{!! Form::hidden('pagato['.$index.']', 0) !!}
					@if($spec->costo!=0)
			               {!! Form::checkbox('pagato['.$index.']', 1, $spec->pagato, ['class' => 'form-control']) !!}
			          @endif
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
	<i>Nessuna settimana inserita!</i><br><br>
@endif


<input id='contatore_e' type='hidden' value="{{$index}}" />
		{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 49%']) !!}
		 <button style="font-size: 15px; width: 49%;" type='button' class="btn btn-primary btn-sm" data-toggle='modal' data-target='#eventspecsOp' data-name='' data-eventid=''><i class="fa fa-plus" aria-hidden="true"></i> <i>Aggiungi specifica</i></button>
		{!! Form::close() !!}


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
