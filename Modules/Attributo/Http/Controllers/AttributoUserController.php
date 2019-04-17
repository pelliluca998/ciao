<?php

namespace Modules\Attributo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\Attributo\Entities\AttributoUser;
use Modules\Attributo\Entities\Attributo;
use Modules\User\Entities\User;
use Yajra\DataTables\DataTables;
use Modules\Attributo\Http\Controllers\DataTables\AttributoUserDataTableEditor;
use Modules\Attributo\Http\Controllers\DataTables\AttributoUserDataTable;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Input;
use Auth;
use Session;

class AttributoUserController extends Controller{

  public function __construct(){
    $this->middleware('permission:view-attributo')->only(['index', 'data']);
    $this->middleware('permission:edit-attributo')->only(['store']);
  }

  public function index(AttributoUserDataTable $dataTable){
    //return $dataTable->render('attributo::index');
  }

  public function store(AttributoUserDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = AttributoUser::query()
    ->select('attributo_users.*', 'attributos.nome as attributo_label', 'attributos.id_type')
    ->leftJoin('attributos', 'attributos.id', 'attributo_users.id_attributo')
    ->where('id_user', $input['id_user'])
    ->orderBy('attributos.nome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $edit = "<button class='btn btn-sm btn-primary btn-block' id='editor_edit'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";

      if(!Auth::user()->can('edit-attributo')){
        $edit = "";
        $remove = "";
      }


      return $edit.$remove;
    })
    ->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->addColumn('valore_label', function ($entity){
      if($entity->id_type > 0){
        $option = TypeSelect::find($entity->valore);
        return $option!=null?$option->option:"";
      }

      switch ($entity->id_type) {
        case Type::TEXT_TYPE:
        return $entity->valore;

        case Type::BOOL_TYPE:
        if($entity->valore == 1){
          return "<i class='far fa-check-circle fa-2x'></i>";
        }else{
          return "<i class='far fa-circle fa-2x'></i>";
        }

        case Type::NUMBER_TYPE:
        return $entity->valore;

        case Type::DATE_TYPE:
        return $entity->valore;
      }

    })
    ->rawColumns(['action', 'valore_label'])
    ->toJson();
  }
}
