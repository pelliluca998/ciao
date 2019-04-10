<?php

namespace Modules\Group\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Group\Entities\GroupUser;
use Illuminate\Http\Request;
use Session;

class GroupUserDataTableEditor extends DataTablesEditor
{
  protected $model = GroupUser::class;

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
  public function removeRules(Model $model){
    return [];
  }

  public function creating(Model $model, array $data){
    return $data;
  }

  public function created(Model $model, array $data){

    return $data;
  }

  public function updating(Model $model, array $data){
    return $data;
  }

}
