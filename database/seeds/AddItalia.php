<?php

use Illuminate\Database\Seeder;
use App\Provincia;
use App\Nazione;
use App\Comune;
use App\Regione;

class AddItalia extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //carico tabella comuni, province e regioni
      DB::statement("SET foreign_key_checks=0");
      Provincia::truncate();
      Nazione::truncate();
      Comune::truncate();
      Regione::truncate();
      $path = 'public/italia.sql';
      DB::statement("SET foreign_key_checks=1");
      DB::unprepared(file_get_contents($path));
      $this->command->info('Tabella Provincia e Nazioni popolate correttamente');
    }
}
