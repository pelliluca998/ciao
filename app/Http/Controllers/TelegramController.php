<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Attributo;
use Auth;
use Session;
use Input;
use Telegram;
//use Telegram\Bot\Actions;
//use Telegram\Bot\Commands\Command;

class TelegramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$response2 = Telegram::setWebhook(['url' => 'https://robertovps.dyndns.org/segresta_admin/'.env('TELEGRAM_BOT_TOKEN').'/webhook']);
        $response = Telegram::getMe();
		$botId = $response->getId();
		$username = $response->getUsername();
		echo $username;
		$response = Telegram::sendPhoto([
			'chat_id' => '311491951',
			'photo' => url("upload/1/user_profile/1479211068.jpg")
]);

$messageId = $response->getMessageId();
		echo "<br><br>".$messageId;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
   public function create(){
		//return view('attributos.create');
	}


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
    	/* $this->validate($request, [
			'nome' => 'required'
		]);
		$input = $request->all();
		$attributo = new Attributo;
		$attributo->nome = $input['nome'];
		$attributo->id_oratorio = Auth::user()->id_oratorio;
		$attributo->id_type = $input['id_type'];
		$attributo->ordine = $input['ordine'];
		$attributo->note = $input['note'];
		$attributo->hidden = '0';

		$attributo->save();
		Session::flash('flash_message', 'Attributo aggiunto!');
		return redirect()->route('attributos.index'); */
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
    public function edit($id)
    {
        //$user = User::findOrFail($id);
      	//return view('users.edit')->withUser($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
    	/*$user = User::findOrFail($id);
        $input = $request->all();
        $user->fill($input)->save();
        Session::flash('flash_message', 'Utente salvato!');
        return redirect('users');*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id){
    		/* $attributo = Attributo::findOrFail($id);
		if($attributo->id_oratorio==Auth::user()->id_oratorio){		
			$attributo->delete();
			Session::flash("flash_message", "Attributo $id cancellato!");
			return redirect()->route('attributos.index');
		}else{
			abort(403, 'Unauthorized action.');
		} */
    }
    
    public function save(Request $request){
		/* $id_attributo = Input::get('id_attributo');
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
				$spec->id_oratorio = Auth::user()->id_oratorio;
				$spec->save();
			}
			$i++;
		}
		Session::flash("flash_message", "Attributi aggiornati!");
		return redirect()->route('attributos.index'); */
	}
}
