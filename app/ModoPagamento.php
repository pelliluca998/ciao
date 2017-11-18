<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModoPagamento extends Model
{
	protected $table = 'modo_pagamento';
    protected $fillable = [
        'id_oratorio', 'label'
    ];
}
