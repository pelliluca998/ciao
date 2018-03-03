<?php

namespace Modules\Attributo\Entities;

use Illuminate\Database\Eloquent\Model;

class Attributo extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'ordine', 'id_oratorio', 'note', 'id_type', 'hidden'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
