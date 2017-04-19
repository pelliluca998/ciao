<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = ['id_oratorio', 'data_inizio', 'data_fine', 'license_type'];
    protected $dates = ['data_inizio', 'data_fine'];
    
    public function getDataInizioAttribute($value){
		return Carbon::parse($value)->format('d/m/Y');
    }
	
	public function setDataInizioAttribute($value){
		$this->attributes['data_inizio'] = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
    }
    
    public function getDataFineAttribute($value){
		return Carbon::parse($value)->format('d/m/Y');
    }
	
	public function setDataFineAttribute($value){
		$this->attributes['data_fine'] = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
    }
}
