<?php

namespace Modules\Event\Entities;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'anno', 'descrizione', 'id_oratorio', 'active', 'firma', 'image', 'color', 'more_subscriptions', 'stampa_anagrafica', 'spec_iscrizione', 'grazie', 'template_file', 'pagine_foglio'];
    public static $pagine_per_foglio = array('1' => "Una pagina per foglio", '2' => "Due pagine per foglio");

    public static function getPaginePerFoglio(){
      return self::$pagine_per_foglio;
    }
}
