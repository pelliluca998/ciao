<?php

namespace Modules\Attributo\Entities;

use Illuminate\Database\Eloquent\Model;

class AttributoUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user', 'id_attributo', 'valore'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
