<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comune extends Model
{
  protected $table = "comuni";
  protected $fillable = ['id_regione', 'id_provincia', 'capoluogo_provincia', 'codice_catastale', 'nome', 'latitudine', 'longitudine'];

  public static function getListaComuni(){
    $comuniList = array();
    $comuni = Comune::orderBy('nome', 'ASC')->get();
    foreach ($comuni as $comune) {
      $comuniList[$comune->id] = $comune->nome;
    }

    asort($comuniList);

    return json_encode($comuniList, JSON_HEX_APOS|JSON_HEX_QUOT);
  }
}
