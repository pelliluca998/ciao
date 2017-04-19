<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Week extends Model
{
    	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_event','from_date', 'to_date'];

    protected $dates = ['from_date', 'to_date'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    
    public function getFromDateAttribute($value){
	return Carbon::parse($value)->format('d/m/Y');
    }
    
    public function getToDateAttribute($value){
	return Carbon::parse($value)->format('d/m/Y');
    }
}
