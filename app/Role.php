<?php namespace App;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Support\Facades\Config;
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

  public function users(){
    return $this->belongsToMany(Config::get('auth.providers.users.model'),Config::get('entrust.role_user_table'),Config::get('entrust.role_foreign_key'),Config::get('entrust.user_foreign_key'));

  }
}
