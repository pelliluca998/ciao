<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bilancio extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $table = 'bilancio';
    protected $fillable = ['id_event', 'id_user', 'id_eventspecvalues', 'id_tipopagamento', 'id_modalita', 'id_cassa', 'descrizione', 'importo', 'data'];
    protected $dates = ['data'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
    
	public function getDataAttribute($value){
		return Carbon::parse($value)->format('d/m/Y');
	}
	
	public function setDataAttribute($value){
		$this->attributes['data'] = Carbon::parse($value)->format('Y-m-d');
	}
}
