<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oratorio extends Model
{
    protected $fillable = [
        'nome', 'email', 'logo', 'sms_sender', 'reg_visible', 'reg_token'
    ];
}
