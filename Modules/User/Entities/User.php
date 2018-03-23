<?php

namespace Modules\User\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Session;

class User extends Authenticatable
{
  use Notifiable;
  use EntrustUserTrait;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['name', 'email', 'password', 'cognome', 'nato_a', 'residente', 'sesso', 'username', 'via', 'nato_il', 'photo', 'cell_number'];

  protected $dates = ['nato_il'];

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = ['password', 'remember_token'];

  public function getNatoIlAttribute($value){
    return Carbon::parse($value)->format('d/m/Y');
  }

  public function setNatoIlAttribute($value){
    $this->attributes['nato_il'] = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
  }

  public function getFullNameAttribute(){
    return $this->cognome." ".$this->name;
  }


  /**
  * Many-to-Many relations with Role.
  *
  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
  */

  public function roles()
  {
    if(Session::has('session_oratorio')){
      if($this->email == config('app.owner_email')){
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'), Config::get('entrust.user_foreign_key'), Config::get('entrust.role_foreign_key'))->where('roles.id_oratorio', null);
      }else{
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'), Config::get('entrust.user_foreign_key'), Config::get('entrust.role_foreign_key'))->where('roles.id_oratorio', Session::get('session_oratorio'));
      }
    }else{
      return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'), Config::get('entrust.user_foreign_key'), Config::get('entrust.role_foreign_key'));
    }
  }

}
