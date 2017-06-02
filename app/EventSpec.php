<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventSpec extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_event', 'ordine', 'valid_for', 'general', 'label', 'descrizione', 'id_type', 'hidden', 'price', 'id_cassa', 'id_tipopagamento', 'id_modopagamento'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
