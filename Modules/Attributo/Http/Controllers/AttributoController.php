<?php

namespace Modules\Attributo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Yajra\DataTables\DataTables;
use Modules\Attributo\Http\Controllers\DataTables\AttributoDataTableEditor;
use Modules\Attributo\Http\Controllers\DataTables\AttributoDataTable;

use Modules\Attributo\Entities\Attributo;
use Modules\Attributo\Entities\AttributoUser;
use Modules\Oratorio\Entities\Type;
use Modules\Oratorio\Entities\TypeSelect;
use Session;
use Input;

class AttributoController extends Controller{

  public function __construct(){
    $this->middleware('permission:view-attributo')->only(['index', 'data']);
    $this->middleware('permission:edit-attributo')->only(['store']);
  }

  public function index(AttributoDataTable $dataTable){
    return $dataTable->render('attributo::index');
  }

  public function store(AttributoDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = Attributo::query()
    ->select('attributos.*')
    ->where('id_oratorio', Session::get('session_oratorio'))
    ->orderBy('nome', 'ASC');

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
    ->addColumn('type_label', function ($entity){
      return Type::getTypeLabel($entity->id_type);
    })
    ->rawColumns(['action'])
    ->toJson();
  }

  public function valore_field(Request $request){
    $input = $request->all();
    //$attributo_user = AttributoUser::find($input['id_attributo_user']);
    $attributo = Attributo::find($input['id_attributo']);
    if($attributo == null){
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'text'));
    }



    if($attributo->id_type > 0){
      $types = TypeSelect::where('id_type', $attributo->id_type)->orderBy('ordine', 'ASC')->pluck('id', 'option');
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'select', 'options' => $types));
    }

    switch ($attributo->id_type) {
      case Type::TEXT_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'text'));

      case Type::BOOL_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'checkbox', 'options' => array('' => 1), 'separator' => '', 'unselectedValue' => '0'));

      case Type::NUMBER_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'attr' => array('type' => 'number')));

      case Type::DATE_TYPE:
      return json_encode(array('label' => 'Valore', 'name' => 'valore', 'type' => 'datetime', 'format' => 'DD/MM/YYYY'));
    }
  }

}
