<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
  protected $table = "province";
  protected $fillable = ['id_regione', 'codice_citta_metropolitana', 'sigla_automobilistica', 'nome', 'latitudine', 'longitudine'];

  public static function getLista(){
    $list = array();
    foreach (Provincia::orderBy('nome', 'ASC')->get() as $p) {
      array_push($list, array('id' => $p->id, 'nome' => $p->nome));//$list[$p->id] = $p->nome;
    }

    return json_encode($list, JSON_HEX_APOS|JSON_HEX_QUOT);
  }


}
