<?php

namespace Modules\Event\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use Modules\Event\Entities\Event;
use Modules\Contabilita\Entities\Bilancio;
use Modules\User\Entities\User;
use Modules\Subscription\Entities\Subscription;
use Module;
use App\License;
use Auth;

class EventSpecValueDataTableEditor extends DataTablesEditor
{
  protected $model = EventSpecValue::class;

  /**
  * Get create action validation rules.
  *
  * @return array
  */
  public function createRules()
  {
    return [];
  }

  public function createMessages(){
    return [];
  }

  /**
  * Get edit action validation rules.
  *
  * @param Model $model
  * @return array
  */
  public function editRules(Model $model)
  {
    return [];
  }

  /**
  * Get remove action validation rules.
  *
  * @param Model $model
  * @return array
  */
  public function removeRules(Model $model)
  {
    return [];
  }

  public function creating(Model $model, array $data)
  {
    return $data;
  }

  public function created(Model $model, array $data)
  {
    return $data;
  }

  public function updating(Model $model, array $data)
  {
    //verifico se mentre l'iscrizione è aperta è stata confermata da amministratore
    $subscription = Subscription::find($model->id_subscription);
    if($subscription->confirmed && !Auth::user()->can('edit-admin-iscrizioni')) return array();
    if(Module::find('contabilita')!=null && License::isValid('contabilita') && !Auth::user()->hasRole('user')){
      if($model->pagato == 0 & $data['pagato'] == 1){
        //specifica pagata ora, da mettere a bilancio
        $id_cassa=0;
        $id_modo=0;
        $id_tipo=0;
        $event_spec = EventSpec::where('id', $model->id_eventspec)->first();
        $subscription = Subscription::find($model->id_subscription);
        $user = User::find($subscription->id_user);
        if($event_spec->id_cassa != null){
          $id_cassa = $event_spec->id_cassa;
        }
        if($event_spec->id_modopagamento != null){
          $id_modo = $event_spec->id_modopagamento;
        }
        if($event_spec->id_tipopagamento != null){
          $id_tipo = $event_spec->id_tipopagamento;
        }

        //salvo in bilancio il costo totale
        $bilancio = new Bilancio;
        $bilancio->id_event = $event_spec->id_event;
        $bilancio->id_admin = Auth::user()->id;
        $bilancio->id_user = $user->id;
        $bilancio->id_eventspecvalues = $model->id;
        $bilancio->id_tipopagamento = $id_tipo;
        $bilancio->id_modalita = $id_modo;
        $bilancio->id_cassa = $id_cassa;
        $bilancio->id_subscription = $subscription->id;
        $bilancio->descrizione = "Incasso da iscrizione";
        $bilancio->importo = floatval($data['costo']);
        $bilancio->data = date('d/m/Y');
        $bilancio->tipo_incasso = 1;
        $bilancio->save();
      }elseif($model->pagato == 1 & $data['pagato'] == 0){
        //ho tolto il pagato alla specifica, rimuovo anche la voce dal bilancio
        $bilancio = Bilancio::where('id_eventspecvalues', $model->id)->first();
        if($bilancio != null){
          $bilancio->delete();
        }
      }
    }

    return $data;
  }

}
