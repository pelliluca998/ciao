<?php

namespace Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Yajra\DataTables\DataTables;
use Modules\Event\Http\Controllers\DataTables\WeekDataTableEditor;
use Modules\Event\Http\Controllers\DataTables\WeekDataTable;
use Session;
use Entrust;
use Auth;
use Input;
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use Carbon;

class WeekController extends Controller
{
	use ValidatesRequests;

	public function __construct(){
		$this->middleware('permission:manage-week');
	}

	public function index(WeekDataTable $dataTable){
		if(null == Session::get('work_event')){
			Session::flash("flash_message", "Prima di modificare le settimane devi selezionare un'evento!");
			return redirect()->route('events.index');
		}else{
			return $dataTable->render('event::week.index');
		}
	}

	public function store(WeekDataTableEditor $editor){
		return $editor->process(request());
	}


	public function data(Request $request, Datatables $datatables){
		$input = $request->all();

		$builder = Week::query()
		->select('weeks.*')
		->where('id_event', Session::get('work_event'))
		->orderBy('from_date', 'ASC');

		return $datatables->eloquent($builder)
		->addColumn('action', function ($week){
			$edit = "<div style='display: inline'><button style='float: left; width: 50%; margin-right: 2px;' class='btn btn-sm btn-primary btn-block' id='editor_edit'><i class='fas fa-pencil-alt'></i> Modifica</button>";
			$remove = "<button style='float: left; width: 48%; margin: 0px;' class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button></div>";
			return $edit.$remove;
		})
		->addColumn('DT_RowId', function ($entity){
			return $entity->id;
		})
		->rawColumns(['action'])
		->toJson();
	}

}
