<?php namespace App;

use Zizaco\Entrust\EntrustRole;
use Session;

class Role extends EntrustRole
{
    protected $fillable = ['name', 'id_oratorio', 'display_name', 'description'];

    public static function getLista(){
      $list = array();
      foreach (Role::where('id_oratorio', Session::get('session_oratorio'))->orderBy('display_name', 'ASC')->get() as $p) {
        array_push($list, array('id' => $p->id, 'nome' => $p->display_name));
      }

      return json_encode($list, JSON_HEX_APOS|JSON_HEX_QUOT);
    }
}
