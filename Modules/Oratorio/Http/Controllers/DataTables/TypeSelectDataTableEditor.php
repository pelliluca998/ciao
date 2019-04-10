<?php

namespace Modules\Oratorio\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Oratorio\Entities\TypeSelect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use App\RoleUser;
use App\Role;

class TypeSelectDataTableEditor extends DataTablesEditor
{
  protected $model = TypeSelect::class;

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
  }

  public function updating(Model $model, array $data)
  {
    return $data;
  }

}
