<?php

namespace Modules\Subscription\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Notifications\IscrizioneApprovata;
use Modules\User\Entities\User;
use Modules\Event\Entities\Event;

class SubscriptionDataTableEditor extends DataTablesEditor
{
  protected $model = Subscription::class;

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
    if($model->confirmed == 0 && $data['confirmed'] == 1){
      $user = User::find($model->id_user);
      $event = Event::find($model->id_event);
      $user->notify(new IscrizioneApprovata($model->id, $event->nome));
    }
    return $data;
  }

}
