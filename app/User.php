<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Carbon\Carbon;

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
}
