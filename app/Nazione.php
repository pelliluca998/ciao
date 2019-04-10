<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Nazione extends Model{
  protected $table = "nazione";
  protected $fillable = ['nome_stato'];

  public static function getLista(){
    $list = array();
    foreach (Nazione::orderBy('nome_stato', 'ASC')->get() as $p) {
      array_push($list, array('id' => $p->id, 'nome' => $p->nome_stato));
    }

    return json_encode($list, JSON_HEX_APOS|JSON_HEX_QUOT);
  }


}
