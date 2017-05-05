<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPagamento extends Model
{
	protected $table = 'tipo_pagamento';
    protected $fillable = [
        'id_oratorio', 'label', 'as_default'
    ];
}
