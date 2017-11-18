<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TypeSelect;

class EventSpec extends Model
{
  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'id_event', 'ordine', 'valid_for', 'general', 'label', 'descrizione', 'id_type', 'hidden', 'price', 'id_cassa', 'id_tipopagamento', 'id_modopagamento'];

  public static function getPrintableValue($id_type, $value){
    if($id_type>0){
      //il valore è da ricercare negli elenchi a scelta multipla creati dall'utente
      $select = TypeSelect::where('id', $value)->get();
      if(count($select)>0){
        return $select[0]->option;
      }else{
        return "n/a";
      }
    }elseif($id_type==-1){
      //spec di tipo testo, torno il valore così come è
      return $value;
    }elseif($id_type==-2){
      //valore booleano
      if($value==0) return "NO";
      else return "SI";
    }elseif($id_type==-3){
      //spec di tipo numero, torno il valore così come è
      return $value;
    }elseif($id_type==-4){
      //gruppo
      $group = Group::where('id', $value)->get();
      if(count($group)>0){
        return $group[0]->nome;
      }else{
        return "n/a";
      }
    }
  }


  }
