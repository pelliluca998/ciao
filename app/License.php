<?php

namespace App;
use Carbon\Carbon;
use Session;

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
    		if($value!=null){
			return Carbon::parse($value)->format('d/m/Y');
		}else{
			return "";
		}
    }
	
	public function setDataFineAttribute($value){
		if($value=="" || $value==null){
			$this->attributes['data_fine'] = null;			
		}else{
			$this->attributes['data_fine'] = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
		}
    }
    
	public static function isValid($moduleName){
		$now = date("Y-m-d");
		$license = License::leftJoin('license_types', 'licenses.license_type', 'license_types.id')->where([['licenses.id_oratorio', Session::get('session_oratorio')], ["modules", "like", "%".$moduleName."%"]])->orWhere([['licenses.data_fine', '>=', $now], ['licenses.data_fine', 'null']])->get();
		if(count($license)>0){
			return true;
		}else{
		  	return false;
		}
	}
}
