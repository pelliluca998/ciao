<?php

namespace Modules\Attributo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Modules\Attributo\Entities\Attributo;
use Session;
use Input;

class AttributoController extends Controller
{
use ValidatesRequests;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('attributo::show');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
	public function create(){
		return view('attributo::create');
	}


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
    	$this->validate($request, [
			'nome' => 'required'
		]);
		$input = $request->all();
		$attributo = new Attributo;
		$attributo->nome = $input['nome'];
		$attributo->id_oratorio = Session::get('session_oratorio');
		$attributo->id_type = $input['id_type'];
		$attributo->ordine = $input['ordine'];
		$attributo->note = $input['note'];
		$attributo->hidden = $input['hidden'];

		$attributo->save();
		Session::flash('flash_message', 'Attributo aggiunto!');
		return redirect()->route('attributo.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    	//return view('attributo.show')->with('id_user', $id_user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request){
        $input = $request->all();
        $attributo = Attributo::findOrFail($input['id_attributo']);
        if($attributo->id_oratorio == Session::get('session_oratorio')){
			return view('attributo::edit')->withAttributo($attributo);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
	public function update(Request $request){
		$input = $request->all();
		$attributo = Attributo::findOrFail($input['id_attributo']);	   
		$attributo->fill($input)->save();
		Session::flash('flash_message', 'Attributo aggiornato!');
		return redirect()->route('attributo.index');
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	public function destroy(Request $request){
		$input = $request->all();
		$attributo = Attributo::findOrFail($input['id_attributo']);
		if($attributo->id_oratorio == Session::get('session_oratorio')){		
			$attributo->delete();
			Session::flash("flash_message", "Attributo ".$input['id_attributo']." cancellato!");
			return redirect()->route('attributo.index');
		}else{
			abort(403, 'Unauthorized action.');
		}
	}
    
    /*public function save(Request $request){
		$id_attributo = Input::get('id_attributo');
		$nome = Input::get('nome');
		$note = Input::get('note');
		$ordine = Input::get('ordine');
		$hidden = Input::get('hidden');
		$id_type = Input::get('id_type');
		$i=0;
		foreach($id_attributo as $id) {
			if($id>0){
				//update
				$spec = Attributo::findOrFail($id);
				$spec->nome = $nome[$i];
				$spec->note = $note[$i];
				$spec->id_type = $id_type[$i];
				$spec->hidden = $hidden[$i];
				$spec->ordine = $ordine[$i];
				$spec->save();
			
			}else{
				$spec = new Attributo;
				$spec->nome = $nome[$i];
				$spec->note = $note[$i];
				$spec->id_type = $id_type[$i];
				$spec->hidden = $hidden[$i];
				$spec->ordine = $ordine[$i];
				$spec->id_oratorio = Session::get('session_oratorio');
				$spec->save();
			}
			$i++;
		}
		Session::flash("flash_message", "Attributi aggiornati!");
		return redirect()->route('attributos.index');
	}*/
}
