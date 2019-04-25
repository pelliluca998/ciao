<?php

use Illuminate\Database\Seeder;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\User\Entities\User;
use App\Role;
use App\Permission;
use App\RoleUser;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {
    $this->call([
      AddItalia::class,
    ]);

    if(Oratorio::count() == 0){

    //Creo un nuovo oratorio
    $oratorio = new Oratorio;
    $oratorio->nome = "Il tuo nuovo Oratorio";
    $oratorio->email = "info@oratorio.it";
    $oratorio->reg_token = md5($oratorio->nome);
    $oratorio->save();
    $this->command->info("Nuovo oratorio creato. Email: info@oratorio.it");
  }

  $role_admin = Role::where('name', 'admin')->first();
  if($role_admin == null){
    //creo i due ruoli base, admin e user
    $role_admin = new Role();
    $role_admin->name = 'admin';
    $role_admin->id_oratorio = $oratorio->id;
    $role_admin->display_name = 'Amministratore';
    $role_admin->description = 'Amministratore della piattaforma';
    $role_admin->save();

    $role_user = new Role();
    $role_user->name = 'user';
    $role_user->id_oratorio = $oratorio->id;
    $role_user->display_name = 'Utente';
    $role_user->description = 'Utente';
    $role_user->save();

    $this->command->info('Ruolo admin e user creati');

    //aggiungo utente Amministratore
    $admin = [
      'name' => 'Amministratore',
      'cognome' => '',
      'email' => 'admin@email.it',
      'password' => Hash::make('admin'),
      'nato_il' => '01/01/2018',
      'id_nazione_nascita' => 118,
      'id_provincia_nascita' => 16,
      'id_comune_nascita' => 16024,
      'id_provincia_residenza' => 16,
      'id_comune_residenza' => 16024,
      'sesso'=>'M',
      'via'=>'via Arena 11',
      'email_verified_at' => Carbon::now()
    ];

    $user = User::create($admin);
    //salvo il link utente-oratorio
    $orat = new UserOratorio;
    $orat->id_user=$user->id;
    $orat->id_oratorio = $oratorio->id;
    $orat->save();
    $this->command->info("Utente amministratore creato correttamente. Email: admin@email.it - Password: admin");

    //Associo il ruolo di amministratore all'utente creato
    $role = new RoleUser;
    $role->user_id = $user->id;
    $role->role_id = $role_admin->id;
    $role->save();
  }

    //Creo i permessi
    foreach(Module::all() as $module){
      $permissions = config($module->getLowerName().'.permissions');
      if($permissions != null && count($permissions) > 0){
        foreach($permissions as $key => $value){
          if(Permission::where('name', $key)->count() == 0){
          $perm = new Permission;
          $perm->name = "$key";
          $perm->display_name = $value;
          $perm->description = $value;
          $perm->save();
          $this->command->info("Creato permesso ".$key);
          $role_admin->attachPermission($perm);
          }
        }
      }
    }

    $this->command->info("Permessi creati");
  }
}
