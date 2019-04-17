<?php

namespace Modules\Oratorio\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Oratorio extends Model
{
	protected $fillable = ['nome', 'nome_parrocchia', 'indirizzo_parrocchia', 'nome_diocesi', 'email', 'logo', 'sms_sender', 'reg_visible', 'reg_token', 'last_login', 'last_id_event'];

	public function getLastLoginAttribute($value){
		return Carbon::parse($value)->format('d/m/Y - H:i:s');
	}
}
