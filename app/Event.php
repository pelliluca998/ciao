<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'anno', 'descrizione', 'id_oratorio', 'active', 'firma', 'image', 'color', 'more_subscriptions', 'stampa_anagrafica', 'spec_iscrizione', 'informativa', 'grazie'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
