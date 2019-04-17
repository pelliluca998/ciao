<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Menu;
use Carbon\Carbon;

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
    if(config('app.force_https')){
      $url->forceScheme('https');
    }

    Schema::defaultStringLength(191);
    Menu::make('SegrestaNavBar', function($menu){});
      Carbon::setLocale('it');
      setlocale(LC_TIME, 'it_IT');


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
