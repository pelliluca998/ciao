<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Input;
use Auth;
use App\TypeSelect;
use App\Type;
use Session;
class TypeSelectController extends Controller
{
	public function save(Request $request){
		$id_option = Input::get('id_option');
		$id_type = Input::get('id_type');
		$options = Input::get('option');
		$ordine = Input::get('ordine');
		//var_dump(Input::all());
		$i=0;
		foreach($options as $option) {
			if($id_option[$i]>0){
				//update
				$spec = TypeSelect::findOrFail($id_option[$i]);
				$spec->option = $option;
				$spec->ordine = $ordine[$i];
				$spec->save();

			}else{
				$spec = new TypeSelect;
				$spec->option = $option;
				$spec->id_type = $id_type[$i];
				$spec->ordine = $ordine[$i];
				$spec->save();
			}
			$i++;
		}
		Session::flash("flash_message", "Opzioni salvate!");
		return redirect()->route('type.index');
	}
	
	public function destroy($id){		
		$sub = TypeSelect::findOrFail($id);
		$type = Type::findOrFail($sub->id_type);
		if($type->id_oratorio==Session::get('session_oratorio')){
			$sub->delete();
			Session::flash("flash_message", "Voce elenco $id cancellata!");
			return redirect()->route('type.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
		
	}
	
	public function show($id_type){
		return view('oratorio::typeselect.show', ['id_type' => $id_type]);
	}
	
	
}
