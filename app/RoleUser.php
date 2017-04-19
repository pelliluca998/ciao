<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['user_id', 'role_id'];
	protected $table = 'role_user';	

}
