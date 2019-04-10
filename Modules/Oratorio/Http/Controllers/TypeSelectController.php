<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Yajra\DataTables\DataTables;
use Modules\Oratorio\Http\Controllers\DataTables\TypeSelectDataTableEditor;
use Modules\Oratorio\Http\Controllers\DataTables\TypeSelectDataTable;

use Input;
use Auth;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Oratorio\Entities\Type;
use Session;
class TypeSelectController extends Controller
{
	public function __construct(){
    $this->middleware('permission:view-select')->only(['index', 'data']);
    $this->middleware('permission:edit-select')->only(['store']);
  }


	public function index(TypeSelectDataTable $dataTable){
  }

  public function store(TypeSelectDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data(Request $request, Datatables $datatables){
    $input = $request->all();

    $builder = TypeSelect::query()
    ->select('type_selects.*')
    ->where('id_type', $input['id_type'])
    ->orderBy('ordine', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
      $edit = "<button class='btn btn-sm btn-primary btn-block' id='editor_edit'><i class='fas fa-pencil-alt'></i> Modifica</button>";
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button>";

			if(!Auth::user()->can('edit-select')){
        $edit = "";
        $remove = "";
      }

      return $edit.$remove;
    })
    ->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }


	// public function save(Request $request){
	// 	$id_option = Input::get('id_option');
	// 	$id_type = Input::get('id_type');
	// 	$options = Input::get('option');
	// 	$ordine = Input::get('ordine');
	// 	//var_dump(Input::all());
	// 	$i=0;
	// 	foreach($options as $option) {
	// 		if($id_option[$i]>0){
	// 			//update
	// 			$spec = TypeSelect::findOrFail($id_option[$i]);
	// 			$spec->option = $option;
	// 			$spec->ordine = $ordine[$i];
	// 			$spec->save();
  //
	// 		}else{
	// 			$spec = new TypeSelect;
	// 			$spec->option = $option;
	// 			$spec->id_type = $id_type[$i];
	// 			$spec->ordine = $ordine[$i];
	// 			$spec->save();
	// 		}
	// 		$i++;
	// 	}
	// 	Session::flash("flash_message", "Opzioni salvate!");
	// 	return redirect()->route('type.index');
	// }

	// public function destroy($id){
	// 	$sub = TypeSelect::findOrFail($id);
	// 	$type = Type::findOrFail($sub->id_type);
	// 	if($type->id_oratorio==Session::get('session_oratorio')){
	// 		$sub->delete();
	// 		Session::flash("flash_message", "Voce elenco $id cancellata!");
	// 		return redirect()->route('type.index');
	// 	}else{
	// 		abort(403, 'Unauthorized action.');
	// 	}
  //
	// }

	// public function show($id_type){
	// 	return view('oratorio::typeselect.show', ['id_type' => $id_type]);
	// }


}
