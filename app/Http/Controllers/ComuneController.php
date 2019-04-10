<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comune;
use App\Provincia;
use Session;
use Storage;
use Form;

class ComuneController extends Controller
{
  public function lista(Request $request){
    $comuniList = array();
    $comuni = Comune::where('id_provincia', $request->input('id_provincia'))->orderBy('nome', 'ASC')->get();
    foreach ($comuni as $comune) {
      array_push($comuniList, array('id' => $comune->id, 'nome' => $comune->nome));
    }

    return json_encode($comuniList, JSON_HEX_APOS|JSON_HEX_QUOT);
  }
}
