<?php

namespace Modules\Group\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Group\Entities\Group;
use Modules\Group\Entities\GroupUser;
use Session;
use Entrust;
use Input;
use Yajra\DataTables\DataTables;
use Modules\Group\Http\Controllers\DataTables\GroupDataTableEditor;
use Modules\Group\Http\Controllers\DataTables\GroupDataTable;

class GroupController extends Controller
{

  public function __construct(){
    $this->middleware('permission:view-gruppo')->only(['index', 'data', 'componenti']);
    $this->middleware('permission:edit-gruppo')->only(['store']);
  }

  /**
  * Display a listing of the resource.
  *
  * @return Response
  */

  public function index(GroupDataTable $dataTable){
    return $dataTable->render('group::index');
  }

  public function store(GroupDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = Group::query()
    ->select('groups.*')
    ->where('id_oratorio', Session::get('session_oratorio'))
    ->orderBy('nome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $edit = "<button class='btn btn-sm btn-primary btn-block' id='editor_edit'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";
      $detail = "<button class='btn btn-sm btn-primary btn-block' onclick='load_componenti(".$entity->id.")'><i class='fas fa-info'></i> Componenti</button>";

      if(!Auth::user()->can('edit-gruppo')){
        $edit = "";
        $remove = "";
      }

      return $edit.$remove.$detail;
    })
    ->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }

  public function componenti($id_gruppo){
    return view('group::componenti')->withGroup(Group::find($id_gruppo));
  }

  // public function report_composer(Request $request){
  //   $input = $request->all();
  //   $id = $input['id_group'];
  //   $group = Group::where('id', $id)->first();
  //   if($group->id_oratorio==Session::get('session_oratorio')){
  //     return view('group::report_composer', ['id_group' => $id]);
  //   }else{
  //     abort(403, 'Unauthorized action.');
  //   }
  //
  // }

  // public function report_generator(Request $request){
  //   $input = $request->all();
  //   $values = Input::get('spec');
  //   $id_group = Input::get('id_group');
  //
  //
  //   return view('group::report_generator', ['input' => $input]);
  // }

}
