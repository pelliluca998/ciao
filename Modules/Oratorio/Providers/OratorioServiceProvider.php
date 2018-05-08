<?php

namespace Modules\Oratorio\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class OratorioServiceProvider extends ServiceProvider
{
  /**
  * Indicates if loading of the provider is deferred.
  *
  * @var bool
  */
  protected $defer = false;

  /**
  * Boot the application events.
  *
  * @return void
  */
  public function boot()
  {
    $this->registerTranslations();
    $this->registerConfig();
    $this->registerViews();
    $this->app['router']->aliasMiddleware('role', \Zizaco\Entrust\Middleware\EntrustRole::class);
    $this->app['router']->aliasMiddleware('license', \App\Http\Middleware\CheckLicense::class);
    //Popolo il menu con un link a questo modulo.
    //Il menu è stato definito in app/Provider/AppServiceProvider.php
    $menuList = Menu::get('SegrestaNavBar');
    $menuList->add("Il tuo oratorio", array("route" => "oratorio.index"))
    ->prepend("<i class='fas fa-cube'></i> ")
    ->data('permissions', ['adminmodule', 'all'])->data('order', 10)
    ->nickname('oratorio');

    $menuList->get('oratorio')
    ->add('Opzioni', array('route'  => 'oratorio.index'))
    ->prepend("<i class='fas fa-cube'></i> ")
    ->data('permissions', ['adminmodule', 'all'])->data('order', 11);

    $menuList->get('oratorio')
    ->add('Elenchi a scelta', array('route'  => 'type.index'))
    ->prepend("<i class='fa fa-bars' aria-hidden='true'></i> ")
    ->data('permissions', ['adminmodule', 'all'])->data('order', 12);

  }

  /**
  * Register the service provider.
  *
  * @return void
  */
  public function register()
  {
    //
  }

  /**
  * Register config.
  *
  * @return void
  */
  protected function registerConfig()
  {
    $this->publishes([
      __DIR__.'/../Config/config.php' => config_path('oratorio.php'),
    ], 'config');
    $this->mergeConfigFrom(
      __DIR__.'/../Config/config.php', 'oratorio'
    );
  }

  /**
  * Register views.
  *
  * @return void
  */
  public function registerViews()
  {
    $viewPath = base_path('resources/views/modules/oratorio');

    $sourcePath = __DIR__.'/../Resources/views';

    $this->publishes([
      $sourcePath => $viewPath
    ]);

    $this->loadViewsFrom(array_merge(array_map(function ($path) {
      return $path . '/modules/oratorio';
    }, \Config::get('view.paths')), [$sourcePath]), 'oratorio');
  }

  /**
  * Register translations.
  *
  * @return void
  */
  public function registerTranslations()
  {
    $langPath = base_path('resources/lang/modules/oratorio');

    if (is_dir($langPath)) {
      $this->loadTranslationsFrom($langPath, 'oratorio');
    } else {
      $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'oratorio');
    }
  }

  /**
  * Get the services provided by the provider.
  *
  * @return array
  */
  public function provides()
  {
    return [];
  }
}
