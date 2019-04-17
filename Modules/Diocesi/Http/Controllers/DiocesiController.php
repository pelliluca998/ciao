<?php

namespace Modules\Diocesi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Oratorio\Entities\Oratorio;
use Yajra\DataTables\DataTables;
use Modules\Diocesi\Http\Controllers\DataTables\OratorioDataTableEditor;
use Modules\Diocesi\Http\Controllers\DataTables\OratorioDataTable;

class DiocesiController extends Controller
{
  public function index_oratori(OratorioDataTable $dataTable){
		return $dataTable->render('diocesi::oratori');
  }

  public function store_oratori(OratorioDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data_oratori(Request $request, Datatables $datatables){
    $input = $request->all();
    $builder = Oratorio::query()
    ->orderBy('nome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
			//$edit = "<div style='display: inline'>".Form::open(['method' => 'GET', 'route' => ['events.edit', $entity->id], 'style' => 'float: left; width: 50%; margin-right: 2px;'])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-pencil-alt'></i> Modifica</button>".Form::close();
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button></div>";
      return $remove;
    })    
		->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }

}
