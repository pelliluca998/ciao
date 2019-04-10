<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Yajra\DataTables\DataTables;
use Modules\Oratorio\Http\Controllers\DataTables\TypeDataTableEditor;
use Modules\Oratorio\Http\Controllers\DataTables\TypeDataTable;
use Session;
use Entrust;
use Input;
use Auth;

class TypeController extends Controller{

  public function __construct(){
    $this->middleware('permission:view-select')->only(['index', 'data', 'opzioni']);
    $this->middleware('permission:edit-select')->only(['store']);
  }

  public function index(TypeDataTable $dataTable){
    return $dataTable->render('oratorio::type.index');
  }

  public function store(TypeDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = Type::query()
    ->select('types.*')
    ->where('id_oratorio', Session::get('session_oratorio'))
    ->orderBy('label', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $edit = "<button class='btn btn-sm btn-primary btn-block' id='editor_edit'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";
      $opzioni = "<button class='btn btn-sm btn-primary btn-block' onclick='load_opzioni(".$entity->id.")'><i class='fas fa-info'></i> Opzioni elenco</button>";

      if(!Auth::user()->can('edit-select')){
        $edit = "";
        $remove = "";
      }

      return $edit.$remove.$opzioni;
    })
    ->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }

  public function opzioni($id_type){
    return view('oratorio::type.opzioni')->withType(Type::find($id_type));
  }

}
