<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['id_user', 'id_event', 'confirmed', 'type', 'consenso_affiliazione', 'consenso_foto', 'consenso_dati_sanitari'];

  public function getCreatedAtAttribute($value){
    return Carbon::parse($value)->format('d/m/Y');
  }
}
