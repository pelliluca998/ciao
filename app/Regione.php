<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regione extends Model
{
  protected $table = "regioni";
  protected $fillable = ['nome', 'latitudine', 'longitudine'];
}
