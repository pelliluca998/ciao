<?php

namespace Modules\Oratorio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Http\Requests;
use Modules\Oratorio\Entities\Oratorio;
use App\OwnerMessage;
use App\Role;
use App\RoleUser;
use App\Permission;
use Modules\User\Entities\User;
use Modules\Oratorio\Entities\UserOratorio;
use Auth;
use Input;
use File;
use Image;
use Session;
use Storage;
use Mail;

class OratorioController extends Controller
{

  public function __construct(){
    $this->middleware('permission:edit-oratorio')->only(['show', 'update']);
  }  

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return Response
  */
  public function show()
  {
    $oratorio = Oratorio::findOrFail(Session::get('session_oratorio'));
    return view('oratorio::edit')->withOratorio($oratorio);;
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  int  $id
  * @return Response
  */
  public function update(Request $request, $id_oratorio){
    $input = $request->all();
    $oratorio = Oratorio::findOrFail($id_oratorio);
    if(Input::hasFile('logo')){
      $file = $request->logo;
      $filename = $request->logo->store('oratorio', 'public');
      $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
      $image = Image::make($path);
      $image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
      $image->save($path);
      $input['logo'] = $filename;
      //cancello la vecchia immagine se presente
      if($oratorio->logo!=""){
        Storage::delete('public/'.$oratorio->logo);
      }
    }
    $oratorio->fill($input)->save();
    Session::flash('flash_message', 'Impostazioni salvate!');
    return redirect()->route('oratorio.index');
  }

  public function update_owner(Request $request){
    $input = $request->all();

    $oratorio = Oratorio::findOrFail($input['id_oratorio']);
    if(Input::hasFile('logo')){
      $file = $request->logo;
      $filename = $request->logo->store('oratorio', 'public');
      $path = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix().$filename;
      $image = Image::make($path);
      $image->resize(500,null, function ($constraint) {$constraint->aspectRatio();});
      $image->save($path);
      $input['logo'] = $filename;
      //cancello la vecchia immagine se presente
      if($oratorio->logo!=""){
        Storage::delete('public/'.$oratorio->logo);
      }
    }
    $oratorio->fill($input)->save();


    Session::flash('flash_message', 'Impostazioni salvate!');
    return redirect()->route('oratorio.showall');
  }

  /**
  * Rimuove l'oratorio specificato
  *
  * @param  int  $id
  * @return Response
  */
  public function destroy(Request $request){
    $input = $request->all();
    $oratorio = Oratorio::findOrFail($input['id_oratorio']);
    $oratorio->delete();
    Session::flash('flash_message', 'Oratorio eliminato');
    return redirect()->route('oratorio.showall');
  }


  /**
  *Richiama la pagina per la modifica dell'oratorio da parte del proprietario
  **/
  public function edit_owner(Request $request){
    $input = $request->all();
    $oratorio = Oratorio::findOrFail($input['id_oratorio']);
    return view('oratorio::gestione.edit')->withOratorio($oratorio);
  }

  public function neworatorio(Request $request){
    return view('neworatorio');
  }
}
