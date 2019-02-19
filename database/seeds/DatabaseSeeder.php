<?php

use Illuminate\Database\Seeder;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Oratorio\Entities\UserOratorio;
use Modules\User\Entities\User;
use App\Role;
use App\Permission;
use App\RoleUser;
use App\LicenseType;
use App\License;
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
        //Creo un nuovo oratorio
        $oratorio = new Oratorio;
        $oratorio->nome = "Il tuo nuovo Oratorio";
        $oratorio->email = "info@oratorio.it";
        $oratorio->reg_token = md5($oratorio->nome);
        $oratorio->save();
        $this->command->info("Nuovo oratorio creato. Email: info@oratorio.it");

        //creo i tre ruoli, owner, admin e user
        $role_owner = new Role();
        $role_owner->name = 'owner';
        $role_owner->id_oratorio = null;
        $role_owner->display_name = 'Owner';
        $role_owner->description = 'Project Owner';
        $role_owner->save();

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

        $this->command->info('Ruolo owner, admin e user creati');

        //Creo i permessi
        $allmodule = new Permission;
        $allmodule->name = "all";
        $allmodule->display_name = "All";
        $allmodule->description = "Can do anything!";
        $allmodule->save();

        $usermodule = new Permission;
        $usermodule->name = "usermodule";
        $usermodule->display_name = "Moduli utente";
        $usermodule->description = "Moduli a cui puoi accedere solo se sei utente";
        $usermodule->save();

        $adminmodule = new Permission;
        $adminmodule->name = "adminmodule";
        $adminmodule->display_name = "Moduli admin";
        $adminmodule->description = "Moduli che solo l'amministratore può visualizzare.";
        $adminmodule->save();

        $ownermodule = new Permission;
        $ownermodule->name = "ownermodule";
        $ownermodule->display_name = "Moduli owner";
        $ownermodule->description = "Moduli che sono il proprietario di Segresta può vedere";
        $ownermodule->save();

        $role_admin->attachPermission($adminmodule);
        $role_admin->attachPermission($usermodule);
        $role_user->attachPermission($usermodule);
        $role_owner->attachPermission($ownermodule);
        $role_owner->attachPermission($adminmodule);
        $role_owner->attachPermission($usermodule);

        $this->command->info("Permessi creati");

        //aggiungo utente Amministratore
        $admin = ['name' => 'Amministratore', 'cognome' => '', 'email' => 'admin@email.it', 'password' => Hash::make('admin'), 'username' => 'admin', 'nato_il' => '01/01/2018', 'nato_a' => 'Bergamo', 'sesso'=>'M', 'residente' => 'Bergamo', 'via'=>'via'];
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
        $role->role_id = $role_owner->id;
        $role->save();

        //creo i tipi di licenze
        $licenza_tipo = new LicenseType;
        $licenza_tipo->name="Free";
        $licenza_tipo->modules='["events", "user", "subscription", "report", "group", "oratorio", "attributo", "elenco"]';
        $licenza_tipo->save();

        $array_modules = ["event", "user", "subscription", "report", "group", "oratorio", "attributo", "elenco"];
        foreach($array_modules as $module){
          $licenza = new License;
          $licenza->id_oratorio = $oratorio->id;
          $licenza->module_name = $module;
          $licenza->data_inizio = Carbon::now()->format('d/m/Y');
          $licenza->data_fine = Carbon::now()->addYear()->format('d/m/Y');
          $licenza->save();
        }



        $this->command->info("Licenza creata");
    }
}
