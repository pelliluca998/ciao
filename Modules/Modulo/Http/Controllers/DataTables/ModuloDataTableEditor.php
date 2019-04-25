<?php

namespace Modules\Modulo\Http\Controllers\DataTables;

use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;
use Illuminate\Database\Eloquent\Model;
use Modules\Modulo\Entities\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use Carbon\Carbon;

class ModuloDataTableEditor extends DataTablesEditor
{
  protected $model = Modulo::class;
  protected $actions = ['create', 'edit', 'remove', 'upload'];
  protected $messages = [
    'label.required' => 'Il nome del modulo è obbligatorio',
    'path_file.required' => 'Il file è obbligatorio',
    'path_file.mimes' => 'Il modulo deve essere in formato .docx!',
  ];

  /**
  * Get create action validation rules.
  *
  * @return array
  */
  public function createRules()
  {
    return [
      'label'  => 'required',
      'path_file' => 'required'
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
      'label'  => 'required',
      'path_file' => 'required'
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

  public function deleted(Model $model, array $data){
    if($model->path_file != ""){
      Storage::delete('public/'.$model->path_file);
    }
  }

  public function updating(Model $model, array $data)
  {
    return $data;
  }

  public function upload(Request $request){
    $input = $request->all();
    if($request->has('upload')){
      $original_name = $request->upload->getClientOriginalName();
      $original_name = explode(".", $original_name);
      $name = camel_case($original_name[0])."_".Carbon::now()->timestamp.".".$original_name[1];
      $filename = $request->upload->storeAs('modulo', $name, 'public');
      return response()->json([
        'data'   => [],
        'upload' => [
          'id' => $filename
        ],
      ]);
    }

    return;

  }

}
