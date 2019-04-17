<?php

namespace Modules\Diocesi\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Oratorio\Entities\Oratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use App\RoleUser;
use App\Role;

class OratorioDataTableEditor extends DataTablesEditor
{
  protected $model = Oratorio::class;
  protected $messages = [
    'nome.required' => 'Il nome dell\'oratorio è obbligatorio',
    'email.required' => 'L\'email è obbligatoria',
  ];

  /**
  * Get create action validation rules.
  *
  * @return array
  */
  public function createRules()
  {
    return [
      'nome'  => 'required',
      'email' => 'required|email|unique:oratorios,email'
    ];
  }

  public function createMessages(){
    return $this->messages;
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
      'nome'  => 'required',
      'email' => 'required|email|unique:oratorios,email,'.$model->id
    ];
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
  }

  public function updating(Model $model, array $data)
  {
    return $data;
  }

}
