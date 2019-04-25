<?php

namespace Modules\Modulo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Modulo\Http\Controllers\DataTables\ModuloDataTableEditor;
use Modules\Modulo\Http\Controllers\DataTables\ModuloDataTable;
use Modules\Modulo\Entities\Modulo;
use Yajra\DataTables\DataTables;
use Form;
use Storage;
use Session;

class ModuloController extends Controller
{
  public function index(ModuloDataTable $dataTable){
		return $dataTable->render('modulo::index');
  }

  public function store(ModuloDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();
    $builder = Modulo::query()
    ->where('id_oratorio', Session::get('session_oratorio'))
    ->orderBy('label', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $edit = "<div style=''><div style='display: flow-root'><button class='btn btn-sm btn-primary btn-block' id='editor_edit' style='float: left; width: 50%; margin-right: 2px;'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button style='float: left; width: 48%; margin: 0px;' class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button></div></div>";
      $download = Form::open(['method' => 'GET', 'route' => ['modulo.download', $entity->id]])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-cloud-download-alt'></i> Download</button>".Form::close();
      return $edit.$remove.$download;
    })
		->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }

  public function download($id){
    $modulo = Modulo::find($id);
    if($modulo!=null){
      $storagePath  = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
			$file = $storagePath.$modulo->path_file;
			if(Storage::disk('public')->exists($modulo->path_file)){
				return response()->download($storagePath.$modulo->path_file);
			}else{
				Session::flash('flash_message', 'Il file non esiste!');
				return redirect()->route('modulo.index');
			}
    }else{
      Session::flash('flash_message', "Il documento non esiste");
  		return redirect()->route('modulo.index');
    }
  }
}
