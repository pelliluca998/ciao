<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OwnerMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'message'];
    
    protected $table = 'owner_message';

    
}
