<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\Week;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Elenco\Entities\Elenco;
use Yajra\DataTables\DataTables;
use Modules\Event\Http\Controllers\DataTables\EventDataTableEditor;
use Modules\Event\Http\Controllers\DataTables\EventDataTable;
use Session;
use Input;
use Entrust;
use Image;
use File;
use Form;
use Storage;
use Auth;

class EventController extends Controller
{
	use ValidatesRequests;

	public function __construct(){
    $this->middleware('permission:view-event')->only(['index', 'data', 'work']);
    $this->middleware('permission:edit-event')->only(['store', 'clone', 'create', 'store_event', 'edit', 'update', 'destroy']);
  }


	public $messages = [
			'nome.required' => 'Inserisci un nome valido per l\'evento',
			'descrizione.required'  => 'Inserisci una descrizione valida',
			'anno.required'  => 'Inserisci un anno valido per l\'evento',
			'template_file.mimes'  => 'Il file template deve avere estensione .docx.',
			'image.mimes' => 'Il logo oratorio deve avere una di queste estensioni: jpeg,jpg,gif,png.'
	];

	/**
	* Display a listing of the resource.
	* @return Response
	*/
	public function index(EventDataTable $dataTable){
    return $dataTable->render('event::index');
  }

	public function store(EventDataTableEditor $editor){
    return $editor->process(request());
  }

	public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = Event::query()
    ->select('events.*')
		->where('id_oratorio', Session::get('session_oratorio'))
    ->orderBy('created_at', 'DESC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
			$edit = "<div style='display: inline'>".Form::open(['method' => 'GET', 'route' => ['events.edit', $entity->id], 'style' => 'float: left; width: 50%; margin-right: 2px;'])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-pencil-alt'></i> Modifica</button>".Form::close();
      $remove = "<button style='float: left; width: 48%; margin: 0px;' class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button></div>";
			$work = Form::open(['method' => 'GET', 'route' => ['events.work', $entity->id]])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-hammer'></i> Lavora con questo evento</button>".Form::close();
			$iscrizioni = Form::open(['method' => 'GET', 'route' => ['subscription.event']])."<input name='id_event' type='hidden' value='".$entity->id."' /><button class='btn btn-sm btn-primary btn-block'><i class='far fa-clock'></i> Mostra iscrizioni</button>".Form::close();
			$specifiche = Form::open(['method' => 'GET', 'route' => ['eventspecs.index']])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-stream'></i> Specifiche evento</button>".Form::close();
			$clona = Form::open(['method' => 'GET', 'route' => ['events.clone', $entity->id]])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-copy'></i> Clona evento</button>".Form::close();

			if(!Auth::user()->can('edit-event')){
				$edit = "";
				$remove = "";
				$work = "";
				$specifiche = "";
				$clona = "";
			}
      return $edit.$remove.$work.$iscrizioni.$specifiche.$clona;
    })
		->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
		->addColumn('descrizione_label', function ($entity){
			$max_length = 900;
      return (strlen(strip_tags($entity->descrizione)) > $max_length) ? substr(strip_tags($entity->descrizione), 0, $max_length) . '...' : strip_tags($entity->descrizione);
    })
    ->rawColumns(['action', 'descrizione_label'])
    ->toJson();
  }


	/**
	* Salva l'id_evento nella sessione
	*
	* @return Response
	*/
	public function work(Request $request, $id_event){
		$oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
		$oratorio->last_id_event = $id_event;
		$oratorio->save();
		Session::put('work_event', $id_event);
		return redirect()->route('subscription.index');
	}

	/**
	** Clona un'evento in un nuovo evento, comprese tutte le specifiche
	**/

	public function clone(Request $request, $id_event){
		$input = $request->all();
		$event = Event::find($id_event);
		$newEvent = $event->replicate();
		$newEvent->nome = "Copia di ".$event->nome;
		$newEvent->save();
		//specs
		$event_specs = EventSpec::where('id_event', $event->id)->get();
		foreach($event_specs as $spec){
			$newSpec = $spec->replicate();
			$newSpec->id_event = $newEvent->id;
			$newSpec->save();
		}
		//weeks
		$weeks = Week::where('id_event', $event->id)->get();
		foreach($weeks as $week){
			$newWeek = $week->replicate();
			$newWeek->id_event = $newEvent->id;
			$newWeek->save();
			//aggiorno l'id delle settimane
			$event_specs = EventSpec::where('id_event', $newEvent->id)->get();
			foreach($event_specs as $spec){
				$pattern = '/"'.$week->id.'"/';
				$sostitution = '"'.$newWeek->id.'"';
				$spec->valid_for = preg_replace($pattern, $sostitution, $spec->valid_for);
				$spec->price = preg_replace($pattern, $sostitution, $spec->price);
				$spec->save();
			}
		}

		//elenco
		$elencos = Elenco::where('id_event', $event->id)->get();
			if(count($elencos)>0){
			foreach($elencos as $elenco){
				$newElenco = $elenco->replicate();
				$newElenco->id_event = $newEvent->id;
				$newElenco->save();
			}
		}

		Session::put('work_event', $newEvent->id);
		Session::flash('flash_message', 'Evento clonato correttamente!');
		return redirect()->route('events.index');

	}

	/**
	* Show the form for creating a new resource.
	* @return Response
	*/
	public function create(){
		return view('event::create');
	}

	public function show($id_event){
		return view('event::show')->withEvent(Event::find($id_event));
	}

	/**
	* Store a newly created resource in storage.
	* @param  Request $request
	* @return Response
	*/
	public function store_event(Request $request){
		$this->validate($request, [
			'nome' => 'required',
			'descrizione' => 'required',
			'anno' =>'required',
			'template_file' => 'mimes:docx,zip',
			'image' => 'mimes:jpeg,jpg,gif,png'
		], $this->messages);
		$input = $request->all();
		$input['id_oratorio'] = Session::get('session_oratorio');
		if(Input::hasFile('image')){
			$file = $request->image;
			$filename = $request->image->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['image'] = $filename;
		}

		if($request->has('id_modulo')){
      $input['id_moduli'] = json_encode($input['id_modulo']);
    }


		$event = Event::create($input);
		Session::flash('flash_message', 'Evento aggiunto! Ora crea le informazioni che gli utenti devono dare durante l\'iscrizione');
		Session::put('work_event', $event->id);
		return redirect()->route('eventspecs.index');
	}


	/**
	* Show the form for editing the specified resource.
	* @return Response
	*/
	public function edit(Request $request, $id_event){
		$input = $request->all();
		$event = Event::findOrFail($id_event);
		if($event->id_oratorio==Session::get('session_oratorio')){
			return view('event::edit')->withEvent($event);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}

	/**
	* Update the specified resource in storage.
	* @param  Request $request
	* @return Response
	*/
	public function update(Request $request, $id_event){
		$input = $request->all();
		$this->validate($request, [
			'nome' => 'required',
			'descrizione' => 'required',
			'anno' =>'required',
			'template_file' => 'mimes:docx,zip',
			'image' => 'mimes:jpeg,jpg,gif,png'
		], $this->messages);

		$event = Event::findOrFail($id_event);
		//$input['active'] = (Input::has('active') && $input['active']) ? true : false;
		//$input['more_subscriptions'] = (Input::has('more_subscriptions') && $input['more_subscriptions']) ? true : false;
		//$input['stampa_anagrafica'] = (Input::has('stampa_anagrafica') && $input['stampa_anagrafica']) ? true : false;
		if(Input::hasFile('image')){
			$file = $request->image;
			$filename = $request->image->store('oratorio', 'public');
			$path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
			$image = Image::make($path);
			$image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
			$image->save($path);
			$input['image'] = $filename;
			//cancello la vecchia immagine se presente
			if($event->image!=""){
				Storage::delete('public/'.$event->image);
			}
		}

		if($request->has('id_modulo')){
      $input['id_moduli'] = json_encode($input['id_modulo']);
    }

		if(Input::has('spec_iscrizione')){
			$input['spec_iscrizione'] = json_encode($input['spec_iscrizione']);
		}


		$event->fill($input)->save();
		Session::flash('flash_message', 'Evento salvato!');
		return redirect()->route('events.index');
	}




	public function strumenti(Request $request){
		if(Session::has('work_event')){
			return view('event::strumenti');
		}else{
			Session::flash('flash_message', 'Per vedere gli strumenti, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}
	}
}
