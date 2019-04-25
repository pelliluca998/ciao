<?php

namespace Modules\Modulo\Entities;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model{
  protected $table = "modulo";
  protected $fillable = ['label', 'path_file', 'id_oratorio'];
}
