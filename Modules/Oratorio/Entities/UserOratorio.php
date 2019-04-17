<?php

namespace Modules\Oratorio\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Carbon\Carbon;

class UserOratorio extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'user_oratorio';
	protected $fillable = ['id_user', 'id_oratorio'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array */
}
