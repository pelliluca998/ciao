<?php

namespace Modules\Diocesi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\User\Entities\User;
use Yajra\DataTables\DataTables;
use Modules\Diocesi\Http\Controllers\DataTables\OratorioDataTableEditor;
use Modules\Diocesi\Http\Controllers\DataTables\OratorioDataTable;
use Modules\Diocesi\Notifications\NotifyAdminNewOratorio;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Role;
use App\RoleUser;
use Session;

class DiocesiController extends Controller
{
  use ValidatesRequests;
  public $messages = [
			'nome.required' => 'Inserisci un nome valido per l\'oratorio',
      'email.required' => 'Inserisci un indirizzo email valido per l\'oratorio',
      'user_name.required' => 'Inserisci un nome valido per l\'utente',
      'user_cognome.required' => 'Inserisci un cognome email valido per l\'utente',
      'user_email.required' => 'Inserisci un indirizzo email valido per l\'utente',
	];

  public function index_oratori(OratorioDataTable $dataTable){
		return $dataTable->render('diocesi::oratori');
  }

  public function store_oratori(OratorioDataTableEditor $editor){
    return $editor->process(request());
  }

  public function data_oratori(Request $request, Datatables $datatables){
    $input = $request->all();
    $builder = Oratorio::query()
    ->orderBy('nome', 'ASC');

    return $datatables->eloquent($builder)
    ->addColumn('action', function ($entity){
			//$edit = "<div style='display: inline'>".Form::open(['method' => 'GET', 'route' => ['events.edit', $entity->id], 'style' => 'float: left; width: 50%; margin-right: 2px;'])."<button class='btn btn-sm btn-primary btn-block'><i class='fas fa-pencil-alt'></i> Modifica</button>".Form::close();
      $remove = "<button class='btn btn-sm btn-danger btn-block' id='editor_remove'><i class='fas fa-trash-alt'></i> Rimuovi</button></div>";
      return $remove;
    })
		->addColumn('DT_RowId', function ($entity){
      return $entity->id;
    })
    ->rawColumns(['action'])
    ->toJson();
  }

  public function create_oratorio(){
    return view('diocesi::create');
  }

  /**
  * Salva il nuovo oratorio
  **/

  public function store_oratorio(Request $request){
    $input = $request->all();
    $this->validate($request, [
			'nome' => 'required',
			'email' => 'required|email',
			'user_name' =>'required',
			'user_cognome' => 'required',
			'user_email' => 'required|email|unique:users,email'
		], $this->messages);

    //salvo l'oratorio
    $oratorio = new Oratorio;
    $oratorio->nome = $input['nome'];
    $oratorio->email = $input['email'];
    $oratorio->save();

    //salvo l'utente
    $user = new User;
    $user->name = $input['user_name'];
    $user->cognome = $input['user_cognome'];
    $user->email = $input['user_email'];
    $user->nato_il = '01/01/2019';
    $user->id_provincia_nascita = 16;
    $user->id_comune_nascita = 16024;
    $user->id_provincia_residenza = 16;
    $user->id_comune_residenza = 16024;
    $user->via = "indirizzo";
    $user->sesso = "M";
    $user->save();

    //salvo il link utente-oratorio
    $orat = new UserOratorio;
    $orat->id_user=$user->id;
    $orat->id_oratorio = $oratorio->id;
    $orat->save();

    //creo i due ruoli base, admin e user
    $role_admin = new Role();
    $role_admin->name = 'admin';
    $role_admin->id_oratorio = $oratorio->id;
    $role_admin->display_name = 'Amministratore';
    $role_admin->description = 'Amministratore';
    $role_admin->save();

    $role_user = new Role();
    $role_user->name = 'user';
    $role_user->id_oratorio = $oratorio->id;
    $role_user->display_name = 'Utente';
    $role_user->description = 'Utente';
    $role_user->save();

    //Associo il ruolo di amministratore all'utente creato
    $role = new RoleUser;
    $role->user_id = $user->id;
    $role->role_id = $role_admin->id;
    $role->save();

    //invio mail di notifica
    $user->notify(new NotifyAdminNewOratorio());

    //fatto!
    Session::flash("flash_message", "Nuovo oratorio creato!");
    return redirect()->route('oratori.index');
  }

}
