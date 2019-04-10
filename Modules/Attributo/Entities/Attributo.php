<?php

namespace Modules\Attributo\Entities;

use Illuminate\Database\Eloquent\Model;

class Attributo extends Model
{
    protected $fillable = ['nome', 'ordine', 'id_oratorio', 'note', 'id_type', 'hidden' ];

    public static function getLista(){
      $list = array();
      foreach (Attributo::orderBy('nome', 'ASC')->get() as $p) {
        array_push($list, array('id' => $p->id, 'nome' => $p->nome));
      }

      return json_encode($list, JSON_HEX_APOS|JSON_HEX_QUOT);
    }

}
