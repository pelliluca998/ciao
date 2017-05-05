<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cassa extends Model
{
	protected $table = 'tipo_cassa';
    protected $fillable = [
        'id_oratorio', 'label', 'as_default'
    ];
}
