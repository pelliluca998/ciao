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
        'id_event', 'valid_for', 'general', 'label', 'descrizione', 'id_type', 'hidden', 'price'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
