<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    		//creo il menu prima che tutto venga inizializzato
    		//così ogni modulo, nel boot, pò aggiungere la sua voce.
        Menu::make('SegrestaNavBar', function($menu){});
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
