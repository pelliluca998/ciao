<?php

namespace Modules\Attributo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\AttributoUser;
use App\Attributo;
use App\User;
use Input;
use Auth;
use Session;

class AttributoUserController extends Controller
{
use ValidatesRequests;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request){
        $input = $request->all();
        $id_user = $input['id_user'];
        $u = User::leftJoin('user_oratorio', 'user_oratorio.id_user', 'users.id')
            ->where([['users.id', $id_user], ['user_oratorio.id_oratorio', Session::get('session_oratorio')]])->get();
        if(count($u)>0){
            return view('attributo::attributouser.create')->with('id_user', $id_user);
        }else{
            abort(403, 'Unauthorized action.');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
	public function show(Request $request){
		$input = $request->all();
		return view('attributo::attributouser.show')->with('id_user', $id_user = $input['id_user']);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
	public function edit(Request $request)
	{
		$input = $request->all();
		$attributo = AttributoUser::findOrFail($input['id_attributouser']);
		$user = User::select('user_oratorio.id_oratorio')->leftJoin('user_oratorio', 'user_oratorio.id_user', 'users.id')->where('users.id', $attributo->id_user)->first();
		if($user->id_oratorio == Session::get('session_oratorio')){		
			return view('attributo::attributouser.edit')->withAttributouser($attributo);
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
		$attributouser = AttributoUser::findOrFail($input['id_attributouser']);
		$attributouser->fill($input)->save();
		Session::flash('flash_message', 'Informazione aggiornata!');
		return redirect()->route('attributouser.show', ['id_user' => $attributouser->id_user]);
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	public function destroy(Request $request){
		$input = $request->all();
		$attributouser = AttributoUSer::findOrFail($input['id_attributouser']);
		$attributo = Attributo::findOrFail($attributouser->id_attributo);
		if($attributo->id_oratorio==Session::get('session_oratorio')){		
			$attributouser->delete();
			Session::flash("flash_message", "Attributo ".$input['id_attributouser']." cancellato!");
			return redirect()->route('attributouser.show', ['id_user' => $attributouser->id_user]);
		}else{
			abort(403, 'Unauthorized action.');
		}
	}


	public function store(Request $request){
		$input = $request->all();
		$attributo = new AttributoUser;
		$attributo->id_user=$input['id_user'];
		$attributo->id_attributo=$input['id_attributo'];
		$attributo->valore=$input['valore'];
		$attributo->save();
		Session::flash('flash_message', 'Informazione aggiunta!');
		return redirect()->route('attributouser.show', ['id_user' => $input['id_user']]);
	}
}
