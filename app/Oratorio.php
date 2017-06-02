<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Oratorio extends Model
{
	protected $fillable = ['nome', 'email', 'logo', 'sms_sender', 'reg_visible', 'reg_token', 'last_login'];

	public function getLastLoginAttribute($value){
		return Carbon::parse($value)->format('d/m/Y - H:i:s');
	}
}
