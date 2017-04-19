<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventSpecValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_eventspec', 'valore', 'id_subscription', 'id_week', 'costo', 'pagato'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
