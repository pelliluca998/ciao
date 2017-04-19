<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsCredit extends Model
{
    protected $fillable = [

        'data', 'id_pacchetto', 'price', 'credit', 'id_oratorio'

    ];
}
