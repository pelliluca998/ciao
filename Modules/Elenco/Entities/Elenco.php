<?php

namespace Modules\Elenco\Entities;

use Illuminate\Database\Eloquent\Model;

class Elenco extends Model
{
    protected $fillable = ['id_event', 'nome', 'colonne'];
}
