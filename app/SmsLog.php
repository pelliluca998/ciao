<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
		'id_user', 'id_oratorio', 'number', 'mittente', 'testo', 'credit', 'esito'];
}
