<?php
use Modules\Event\Entities\Event;
use Modules\Event\Entities\Week;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Event\Entities\EventSpec;
use Modules\User\Entities\Group;
use Modules\Famiglia\Entities\Famiglia;
use Modules\Famiglia\Entities\ComponenteFamiglia;

//specifiche dell'evento
$specs = (new EventSpec)
->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.price', 'event_specs.acconto', 'event_specs.obbligatoria')
->where([['id_event', $event->id], ['event_specs.general', 1]])
->orderBy('event_specs.ordine', 'ASC')
->get();

$weeks = Week::select('id', 'from_date', 'to_date')->where('id_event', $event->id)->orderBy('from_date', 'asc')->get();
$index = 0;
$oratorio = Oratorio::find(Session::get('session_oratorio'));

$padre = ComponenteFamiglia::getPadre($id_user);
$madre = ComponenteFamiglia::getMadre($id_user);
?>

@extends('layouts.app')
@section('content')

<div class="container">
	<div class="row">
		<div class="col">
			<div class="card bg-transparent border-0">
				<h1><i class='fas fa-user'></i> Iscrizione all'evento <i> {{$event->nome}}</i></h1>
				<p class="lead">Inserisci le informazioni richieste per questo evento, poi clicca su "Salva"</p>
				<hr>
			</div>
		</div>
	</div>

	<div class="row justify-content-center" style="margin-top: 20px;">
		<div class="col-6">
			<div class="card">
				<div class="card-body">
					{!! Form::open(['route' => 'subscribe.savesubscribe', 'id' => 'prova']) !!}

					@if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner'))
					{!! Form::hidden('type', 'ADMIN') !!}
					{!! Form::hidden('confirmed', '1') !!}
					@else
					{!! Form::hidden('type', 'WEB') !!}
					{!! Form::hidden('confirmed', '0') !!}
					@endif

					{!! Form::hidden('id_event', $event->id) !!}


					<nav>
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							@if($event->select_famiglia)
							<a class="nav-item nav-link active" id="nav-famiglia-tab" data-toggle="tab" href="#nav-famiglia" role="tab" aria-controls="nav-famiglia" aria-selected="true">Utente</a>
							<a class="nav-item nav-link" id="nav-generali-tab" data-toggle="tab" href="#nav-generali" role="tab" aria-controls="nav-generali" aria-selected="false">Informazioni generali</a>
							@else
							<a class="nav-item nav-link active" id="nav-generali-tab" data-toggle="tab" href="#nav-generali" role="tab" aria-controls="nav-generali" aria-selected="true">Informazioni generali</a>
							@endif

							@if(count($weeks)>0)
							<a class="nav-item nav-link" id="nav-settimanali-tab" data-toggle="tab" href="#nav-settimanali" role="tab" aria-controls="nav-settimanali" aria-selected="false">Informazioni settimanali</a>
							@endif
							<a class="nav-item nav-link" id="nav-salva-tab" data-toggle="tab" href="#nav-salva" role="tab" aria-controls="nav-salva" aria-selected="false">Salva</a>
						</div>
					</nav>

					<div class="tab-content" id="nav-tabContent">
						<!--  Se abilitata la sezione di un membro della famiglia, genero un select con nome variabile id_user-->
						@if($event->select_famiglia)
						<div class="tab-pane fade show active" id="nav-famiglia" role="tabpanel" aria-labelledby="nav-famiglia-tab" style="margin-top: 20px;">
							{!! Form::label('id_user', 'Seleziona un componente della famiglia per cui stai eseguendo l\'iscrizione all\'evento') !!}
							{!! Form::select('id_user', ComponenteFamiglia::getComponenti($id_user), null, ['class' => 'form-control', 'required'])!!}
							<br><br>
							<button class='btn btn-lg btn-success' id="to_general"><i class="fas fa-angle-double-right"></i> Avanti</button>
						</div>
						@else
						{!! Form::hidden('id_user', $id_user) !!}
						@endif

						<div class="tab-pane fade @if(!$event->select_famiglia) show active @endif" id="nav-generali" role="tabpanel" aria-labelledby="nav-generali-tab" style="margin-top: 20px;">
							@foreach($specs as $spec)
							<?php
							$price = json_decode($spec->price, true);
							$acconto = json_decode($spec->acconto, true);
							if(count($price)==0) $price[0]=0;
							if(count($acconto)==0) $acconto[0]=0;
							$txt_obbligatoria = "";
							if($spec->obbligatoria){
								$txt_obbligatoria = "<i style='color: red'>richiesto</i>";
							}
							?>
							<div class="form-row" style="{!! (($spec->hidden && Auth::user()->hasRole('user'))?'display:none':'display:') !!}">
								{!! Form::hidden('id_spec['.$index.']', $spec->id) !!}
								{!! Form::hidden('id_week['.$index.']', 0) !!}

								<!-- Nome e valore della specifica -->
								<div class="form-group col">
									{!! Form::label($spec->id, $spec->label) !!} {!! $txt_obbligatoria !!}
									@if(strlen($spec->descrizione)>0)
									<p> {{ $spec->descrizione }} </p>
									@endif
									@if($spec->id_type > 0)
									{!! Form::select('specs['.$index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), '', ['class' => 'form-control', 'placeholder'=>'Seleziona un\'opzione', ($spec->obbligatoria==1)?'required':''])!!}
									@else
									@if($spec->id_type == Type::TEXT_TYPE)
									{!! Form::text('specs['.$index.']', '', ['class' => 'form-control', ($spec->obbligatoria==1)?'required':'']) !!}
									@elseif($spec->id_type == Type::BOOL_TYPE)
									{!! Form::hidden('specs['.$index.']', 0) !!}
									{!! Form::checkbox('specs['.$index.']', 1, '', ['class' => 'form-control']) !!}
									@elseif($spec->id_type == Type::NUMBER_TYPE)
									{!! Form::number('specs['.$index.']', '', ['class' => 'form-control', ($spec->obbligatoria==1)?'required':'']) !!}
									@elseif($spec->id_type == Type::DATE_TYPE)
									{!! Form::text('specs['.$index.']', '', ['class' => 'form-control date', ($spec->obbligatoria==1)?'required':'']) !!}
									@endif
									@endif
								</div>

								<!-- COSTO  -->
								<div class="form-group col-2">
									{!! Form::label('costo['.$index.']', "Prezzo") !!}
									@if(Auth::user()->hasRole('user'))
									{!! Form::hidden('costo['.$index.']', $price[0]) !!}
									{{ number_format(floatval($price[0]), 2, ',', '') }}€
									@else
									{!! Form::number('costo['.$index.']', $price[0], ['class' => 'form-control', 'step' => '0.1']) !!}
									@endif
								</div>

								<!-- ACCONTO  -->
								<div class="form-group col-2">
									{!! Form::label('acconto['.$index.']', "Acconto") !!}
									@if(Auth::user()->hasRole('user'))
									{!! Form::hidden('acconto['.$index.']', 0) !!}
									{{ number_format(floatval($price[0]), 2, ',', '') }}€
									@else
									{!! Form::number('acconto['.$index.']', $acconto[0], ['class' => 'form-control', 'step' => '0.1']) !!}
									@endif
								</div>

								<!-- PAGATO  -->
								@if(Auth::user()->hasRole('user'))
								{!! Form::hidden('pagato['.$index.']', 0) !!}
								@else
								<div class="form-group col-2">
									{!! Form::label('pagato['.$index.']', "Pagato") !!}
									{!! Form::hidden('pagato['.$index.']', 0) !!}
									{!! Form::checkbox('pagato['.$index.']', 1, false, ['class' => 'form-control']) !!}
								</div>
								@endif


							</div>
							@php
							$index++
							@endphp
							@endforeach

							<button class='btn btn-lg btn-success' id="to_settimana"><i class="fas fa-angle-double-right"></i> Avanti</button>

						</div>

						@if(count($weeks)>0)
						<div class="tab-pane fade" id="nav-settimanali" role="tabpanel" aria-labelledby="nav-settimanali-tab" style="margin-top: 20px;">
							@foreach($weeks as $w)
							<?php
							$specs = (new EventSpec)
							->select('event_specs.id_type', 'event_specs.hidden', 'event_specs.id', 'event_specs.label', 'event_specs.descrizione', 'event_specs.valid_for', 'event_specs.price', 'event_specs.acconto', 'event_specs.obbligatoria')
							->where([['id_event', $event->id], ['event_specs.general', 0]])
							->orderBy('event_specs.ordine', 'ASC')
							->get();
							?>

							@if(count($specs)>0)
							<h2>Settimana {{$loop->index+1}} - dal {{$w->from_date}} al {{$w->to_date}}</h2>
							@endif

							@foreach($specs as $spec)
							<?php
							$valid = json_decode($spec->valid_for, true);
							$price = json_decode($spec->price, true);
							$acconto = json_decode($spec->acconto, true);
							if(count($price)==0) $price[$w->id]=0;
							if(count($acconto)==0) $acconto[$w->id]=0;
							$txt_obbligatoria = "";
							if($spec->obbligatoria){
								$txt_obbligatoria = "<i style='color: red'>richiesto</i>";
							}
							?>
							@if($valid[$w->id]==1)
							<div class="form-row" style="{!! (($spec->hidden && Auth::user()->hasRole('user'))?'display:none':'display:') !!}">
								{!! Form::hidden('id_spec['.$index.']', $spec->id) !!}
								{!! Form::hidden('id_week['.$index.']', $w->id) !!}

								<!-- Nome e valore della specifica -->
								<div class="form-group col">
									{!! Form::label($spec->id, $spec->label) !!} {!! $txt_obbligatoria !!}
									@if(strlen($spec->descrizione)>0)
									<p> {{$spec->descrizione}} </p>
									@endif

									@if($spec->id_type>0)
									{!! Form::select('specs['.$index.']', TypeSelect::where('id_type', $spec->id_type)->orderBy('ordine', 'ASC')->pluck('option', 'id'), $spec->valore, ['class' => 'form-control', 'placeholder'=>'Seleziona un\'opzione', ($spec->obbligatoria==1)?'required':''])!!}
									@else
									@if($spec->id_type==-1)
									{!! Form::text('specs['.$index.']', $spec->valore, ['class' => 'form-control', ($spec->obbligatoria==1)?'required':'']) !!}
									@elseif($spec->id_type==-2)
									{!! Form::hidden('specs['.$index.']', 0) !!}
									{!! Form::checkbox('specs['.$index.']', 1, $spec->valore, ['class' => 'form-control']) !!}
									@elseif($spec->id_type==-3)
									{!! Form::number('specs['.$index.']', $spec->valore, ['class' => 'form-control', ($spec->obbligatoria==1)?'required':'']) !!}
									@endif
									@endif
								</div>

								<!--  COSTO -->
								<div class="form-group col-2">
									{!! Form::label('costo['.$index.']', "Costo") !!}
									@if(Auth::user()->hasRole('user'))
									{!! Form::hidden('costo['.$index.']', $price[$w->id]) !!}
									{{ number_format(floatval($price[$w->id]), 2, ',', '') }}€
									@else
									{!! Form::number('costo['.$index.']', $price[$w->id], ['class' => 'form-control', 'step' => '0.1']) !!}
									@endif
								</div>

								<!--  ACCONTO -->
								<div class="form-group col-2">
									{!! Form::label('acconto['.$index.']', "Acconto") !!}
									@if(Auth::user()->hasRole('user'))
									{!! Form::hidden('acconto['.$index.']', $acconto[$w->id]) !!}
									{{ number_format(floatval($acconto[$w->id]), 2, ',', '') }}€
									@else
									{!! Form::number('acconto['.$index.']', $acconto[$w->id], ['class' => 'form-control', 'step' => '0.1']) !!}
									@endif
								</div>
								<!-- PAGATO  -->
								@if(Auth::user()->hasRole('user'))
								{!! Form::hidden('pagato['.$index.']', 0) !!}
								@else
								<div class="form-group col-2">
									{!! Form::label('pagato['.$index.']', "Pagato") !!}
									{!! Form::hidden('pagato['.$index.']', 0) !!}
									{!! Form::checkbox('pagato['.$index.']', 1, false, ['class' => 'form-control']) !!}
								</div>
								@endif
							</div>
							@php
							$index++
							@endphp
							@endif <!-- endid valid  -->
							@endforeach <!-- end foreach specifiche  -->

							@endforeach <!-- end foreach settimane  -->

							<button class='btn btn-lg btn-success' id="to_salva"><i class="fas fa-angle-double-right"></i> Avanti</button>
						</div>
						@endif


						<div class="tab-pane fade" id="nav-salva" role="tabpanel" aria-labelledby="nav-salva-tab" style="margin-top: 20px;">
							<h3 style="text-align: center">
								Raccolta dati per l’attività "{{ $event->nome }}" (art. 16, L. n. 222/85) promosse da {{ $oratorio->nome_parrocchia }}
							</h3>
							<p>
								Tenuto conto di quanto previsto dall’art. 91 del Regolamento UE 2016/679, il trattamento dei dati personali da Voi conferiti compilando
								le pagine precedenti è soggetto al Decreto Generale della CEI "Disposizioni per la tutela del diritto alla buona fama e alla riservatezza
								dei dati relativi alle persone dei fedeli, degli enti ecclesiastici e delle aggregazioni laicali" del 24 maggio 2018.
							</p>
							<p>
								Ai sensi degli articoli 6 e 7 del Decreto Generale CEI si precisa che:
							</p>
							<ol type="a">
								<li>il titolare del trattamento è l’ente {{ $oratorio->nome_parrocchia }}, con sede in {{ $oratorio->indirizzo_parrocchia}},
									legalmente rappresentata dal parroco pro tempore;</li>
								<li>per contattare il titolare del trattamento può essere utilizzata la mail {{ $oratorio->email }};</li>
								<li>i dati da Voi conferiti sono richiesti e saranno trattati unicamente per organizzare le attività inerenti l'evento "{{ $event->nome }}" promosse da {{ $oratorio->nome_parrocchia }};</li>
								<li>i medesimi dati non saranno comunicati a soggetti terzi, fatto salvo l’ente {{ $oratorio->nome_diocesi }} e le altre persone giuridiche canoniche,
									se e nei limiti previsti dall’ordinamento canonico, che assumono la veste di contitolari del trattamento;</li>
								<li>i dati conferiti saranno conservati fino al termine delle attività inerenti l'evento "{{ $event->nome }}";
									alcuni dati potranno essere conservati anche oltre tale periodo se e nei limiti in cui tale conservazione risponda ad un legittimo interesse di {{ $oratorio->nome_parrocchia }};</li>
								<li>l'interessato può chiedere a {{ $oratorio->nome_parrocchia }} l'accesso ai dati personali (propri e del figlio/della figlia),
									la rettifica o la cancellazione degli stessi, la limitazione del trattamento che lo riguarda oppure può opporsi al loro trattamento;
									tale richiesta avrà effetto nei confronti di tutti i contitolari del trattamento;</li>
								<li>l’interessato può, altresì, proporre reclamo all’Autorità di controllo</li>
							</ol>
							<p>
								<b>Tenuto conto che il trattamento dei dati personali sopra indicati è limitato alle sole finalità di cui alla lett. c) dell’Informativa,
								considerato che il trattamento dei dati personali È NECESSARIO per permettere alla Parrocchia di realizzare in sicurezza le iniziative sopra indicate
								(compilazione elenchi interni per controllo presenze, ...) e che dunque l’eventuale diniego al trattamento dei dati personali sopra indicati
								impedisce alla medesima di accogliere la richiesta di iscrizione/partecipazione, letta e ricevuta l’Informativa Privacy,
								prendiamo atto di quanto sopra in ordine al trattamento dei dati per le finalità indicate alla lettera c) dell’Informativa.</b>
							</p>
							<p>
								<b>Inoltre</b>, premesso che {{ $oratorio->nome_parrocchia }} intenderebbe poter conservare ed utilizzare
								(ad esempio tramite creazione di mail-list o elenco telefonico) i dati conferiti in queste pagine <b>ANCHE</b> per comunicare le future iniziative ed attività da essa promosse;
								<br>che il predetto trattamento avrà termine qualora sia revocato il presente consenso;
								<br>tenuto conto che il trattamento per le suddette finalità <b>NON È NECESSARIO</b> per consentire alla Parrocchia di accogliere e dar corso
								alla richiesta di iscrizione/partecipazione di cui sopra e, dunque, l’eventuale diniego non impedisce l’accoglimento della medesima, letta e ricevuta l’Informativa Privacy
							</p>
							<div class="form-row">
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_affiliazione', 'Esprimiamo il consenso') !!}
									{!! Form::radio('consenso_affiliazione', 1, null, ['class' => 'form-control', 'required']) !!}
								</div>
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_affiliazione', 'Neghiamo il consenso') !!}
									{!! Form::radio('consenso_affiliazione', 0, null, ['class' => 'form-control', 'required']) !!}
								</div>
							</div>

							<h3 style="text-align: center">
								Informativa relativa alla tutela della riservatezza, in relazione ai dati personali raccolti per le attività educative della parrocchia.
							</h3>
							<p>
								Il trattamento dei dati sanitari forniti è soggetto alla normativa canonica in vigore.
								{{ $oratorio->nome_parrocchia }} dichiara che i dati conferiti saranno utilizzati, quando necessario, ogniqualvolta Vostro/a figlio/a sarà affidato
								alle sue cure nell’ambito della conduzione dell'evento "{{ $event->nome }}" e non saranno diffusi o comunicati ad altri soggetti.
								L’eventuale mancanza di comunicazione di elementi sanitari necessari al sicuro accudimento del minore ricade sotto l’esclusiva responsabilità della famiglia;
								il relativo consenso in tema di tutela della riservatezza È NECESSARIO per permettere alla Parrocchia di realizzare in sicurezza le iniziative inerenti il Cre.
								È comunque possibile richiedere alla Parrocchia la cancellazione dei propri dati.
							</p>

							<div class="form-row">
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_dati_sanitari', 'Esprimiamo il consenso') !!}
									{!! Form::radio('consenso_dati_sanitari', 1, null, ['class' => 'form-control', 'required']) !!}
								</div>
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_dati_sanitari', 'Neghiamo il consenso') !!}
									{!! Form::radio('consenso_dati_sanitari', 0, null, ['class' => 'form-control', 'required']) !!}
								</div>
							</div>

							<h3 style="text-align: center">
								Informativa e CONSENSO al trattamento di fotografie e video
							</h3>
							<p>
								Gentili Signori, desideriamo informarVi che il Regolamento UE 2016/679 e il Decreto Generale della CEI del 24 maggio 2018 prevedono la tutela
								delle persone ogniqualvolta sono trattati dati che le riguardano.
								Nel rispetto della normativa vigente il trattamento dei dati sarà svolto da {{ $oratorio->nome_parrocchia }} in modo lecito, corretto e trasparente
								nei confronti dell'interessato, assicurando la tutela dei suoi diritti.
								Ai sensi degli articoli 13 e seguenti del Regolamento UE 2016/679 e degli articoli 6 e seguenti del Decreto Generale CEI si precisa che:
							</p>
							<ol>
								<li>il titolare del trattamento è l’ente {{ $oratorio->nome_parrocchia }},
									con sede in {{ $oratorio->indirizzo_parrocchia }}, legalmente rappresentata dal parroco pro tempore; </li>
									<li>per contattare il titolare del trattamento può essere utilizzata la mail {{ $oratorio->email }};</li>
									<li>le foto ed i video del figlio/della figlia saranno trattati unicamente per:
									<ul>
										<li>dare evidenza delle attività promosse dalla Parrocchia alle quali ha partecipato il figlio/la figlia,
											anche attraverso pubblicazioni cartacee (bollettino parrocchiale, bacheca in oratorio, volantino …),
											nonché la 	pagina web e i “social” della Parrocchia;
										</li>
										<li>
											finalità di archiviazione e documentazione delle attività promosse dalla Parrocchia.
										</li>
									</ul>
									<li>le foto ed i video non saranno comunicati a soggetti terzi, fatto salvo l’ente Diocesi di Bergamo e le altre persone giuridiche canoniche;</li>
									<li>{{ $oratorio->nome_parrocchia }} si impegna ad adottare idonei strumenti a protezione delle immagini pubblicate sulla pagina web e sui “social”;</li>
									<li>le foto ed i video saranno conservati e trattati fino a revoca del consenso;</li>
									<li>l'interessato può chiedere a {{ $oratorio->nome_parrocchia }} l'accesso ai dati personali, la rettifica o la cancellazione degli stessi,
										la limitazione del trattamento oppure può opporsi al loro trattamento; </li>
									<li>l’interessato può, altresì, proporre reclamo all’Autorità di controllo; </li>
									<li>{{ $oratorio->nome_parrocchia }} non utilizza processi decisionali automatizzati, compresa la profilazione di cui all’articolo 22, paragrafi 1 e 4 del Regolamento UE 2016/679.</li>
								</li>
							</ol>
							<p>
								Noi sottoscritti, genitori del minore oggetto di questa iscrizione:
							</p>
							<ul>
								<li><b>Padre</b>: {{ $padre != null?$padre->full_name:'' }}</li>
								<li><b>Madre</b>: {{ $madre != null?$madre->full_name:'' }}</li>
							</ul>

							<div class="form-row">
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_foto', 'Autorizziamo') !!}
									{!! Form::radio('consenso_foto', 1, null, ['class' => 'form-control', 'required']) !!}
								</div>
								<div class="form-group col" style="text-align: center">
									{!! Form::label('consenso_foto', 'Non autorizziamo') !!}
									{!! Form::radio('consenso_foto', 0, null, ['class' => 'form-control', 'required']) !!}
								</div>
							</div>


							<p>
								{{ $oratorio->nome_parrocchia }} a trattare le foto ed i video relativi a nostro/a figlio/figlia secondo le finalità
								e nei limiti indicati nel foglio informativo che ci è stato consegnato.
							</p>




							<button class='btn btn-lg btn-success' type="submit"><i class="far fa-save"></i> Salva</button>
						</div>
					</div>


					{!! Form::close() !!}

				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
var form = document.getElementById("prova");
form.noValidate = true;

form.onsubmit = function(e) {
  e.preventDefault();
  this.reportValidity();
  if(this.checkValidity()) return form.submit();
  alert('Alcuni campi obbligatori non sono stati compilati!');
}


$('#to_settimana').on('click', function (e) {
	e.preventDefault();
	if("{{ count($weeks) }}" > 0){
		$('#nav-tab a[href="#nav-settimanali"]').tab('show')
	}else{
		$('#nav-tab a[href="#nav-salva"]').tab('show')
	}
});

$('#to_salva').on('click', function (e) {
	e.preventDefault();
	$('#nav-tab a[href="#nav-salva"]').tab('show')
});

$('#to_general').on('click', function (e) {
	e.preventDefault();
	$('#nav-tab a[href="#nav-generali"]').tab('show')
});

$(document).ready(function(){
  $('.date').datetimepicker({
    locale: 'it',
    sideBySide: true,
    format: 'DD/MM/YYYY'
  });

});

</script>
@endpush
