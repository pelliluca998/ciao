<?php

namespace Modules\Event\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Event\Entities\Week;

class WeekDataTableEditor extends DataTablesEditor
{
  protected $model = Week::class;

  /**
  * Get create action validation rules.
  *
  * @return array
  */
  public $message = [
    'from_date.required' => 'Devi specificare una data d\'inizio',
    'to_date.required' => 'Devi specificare una data di fine',
    'from_data.date_format:d/m/Y' => 'Devi inserire una data nel formato 01/01/2019',
    'to_data.date_format:d/m/Y' => 'Devi inserire una data nel formato 01/01/2019',
  ];

  public function createRules()
  {
    return [
        'from_date' => 'required|date_format:d/m/Y',
        'to_date' => 'required|date_format:d/m/Y',
    ];
  }

  public function createMessages(){
    return $this->message;
  }

  /**
  * Get edit action validation rules.
  *
  * @param Model $model
  * @return array
  */
  public function editRules(Model $model)
  {
    return [
        'from_date' => 'required|date_format:d/m/Y',
        'to_date' => 'required|date_format:d/m/Y',
    ];
  }

  public function editMessages(){
    return $this->message;
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
    return $data;
  }

}
