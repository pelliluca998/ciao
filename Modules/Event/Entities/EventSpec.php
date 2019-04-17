<?php

namespace Modules\Event\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Oratorio\Entities\TypeSelect;
use Modules\Oratorio\Entities\Type;
use Session;
use Modules\Event\Entities\Week;

class EventSpec extends Model
{
  protected $fillable = ['id_event', 'ordine', 'valid_for', 'general', 'label', 'descrizione', 'id_type', 'hidden',
  'price', 'acconto', 'id_cassa', 'id_tipopagamento', 'id_modopagamento', 'descrizione'];

  public static function getPrintableValue($id_type, $value){
    if($id_type > 0){
      //il valore è da ricercare negli elenchi a scelta multipla creati dall'utente
      $select = TypeSelect::where('id', $value)->get();
      if(count($select)>0){
        return $select[0]->option;
      }else{
        return "n/a";
      }
    }

    switch($id_type){
      case Type::TEXT_TYPE:
      return $value;
      case Type::BOOL_TYPE:
      if($value==0) return "NO";
      else return "SI";
      case Type::NUMBER_TYPE:
      return $value;
      case Type::DATE_TYPE:
      return $value;
    }
  }

  public static function getSpecsForSelect(){
    $options = "";
    //specifiche generali
    $specs = EventSpec::where([['id_event', Session::get('work_event')], ['general', '1']])->get();
    $options .= "<option value='0' disabled> --- Generali</option>";
    foreach($specs as $spec){
      $price = json_decode($spec->price, true);
      $acconto = json_decode($spec->acconto, true);
      $options .= "<option value='".$spec->id."' data-costo='".$price[0]."' data-acconto='".$acconto[0]."' data-week='0'>".$spec->label."</option>";
    }

    //specifiche Settimanali
    $weeks = Week::select('id', 'from_date', 'to_date')->where('id_event', Session::get('work_event'))->orderBy('from_date', 'asc')->get();
    if(count($weeks)>0){
      foreach($weeks as $week){
        $specs = EventSpec::where([['id_event', Session::get('work_event')], ['general', '0'], ['valid_for', 'like', '%"'.$week->id.'":"1"%']])->get();
        if(count($specs)>0){
          $options .= "<option value='0' disabled> --- Settimana dal ".$week->from_date." al ".$week->to_date."</option>";
          foreach($specs as $spec){
            $price = json_decode($spec->price, true);
            $acconto = json_decode($spec->acconto, true);
            $options .= "<option value='".$spec->id."' data-costo='".$price[$week->id]."' data-acconto='".$acconto[$week->id]."' data-week='".$week->id."'>".$spec->label."</option>";
          }
        }
      }
    }

    return $options;
  }


}
