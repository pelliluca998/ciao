<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_user', 'id_event', 'confirmed', 'type', 'consenso_affiliazione', 'consenso_foto', 'consenso_dati_sanitari'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
