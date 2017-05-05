<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElencoValue extends Model
{
    protected $fillable = ['id_elenco', 'id_user', 'valore'];
}
