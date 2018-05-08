<?php

namespace Modules\Subscription\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class SubscriptionServiceProvider extends ServiceProvider
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
    /*$menuList->add("Iscrizioni", array("route" => "subscription.index"))
    ->prepend("<i class='fa fa-flag' aria-hidden='true'></i> ")
    ->data('permissions', ['adminmodule', 'all'])->data('order', 30);*/

    $menuList->get('eventi')
    ->add('Iscrizioni', array('route'  => 'subscription.index'))
    ->prepend("<i class='fas fa-flag' aria-hidden='true'></i> ")
    ->data('permissions', ['adminmodule', 'all'])->data('order', 31);
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
      __DIR__.'/../Config/config.php' => config_path('subscription.php'),
    ], 'config');
    $this->mergeConfigFrom(
      __DIR__.'/../Config/config.php', 'subscription'
    );
  }

  /**
  * Register views.
  *
  * @return void
  */
  public function registerViews()
  {
    $viewPath = base_path('resources/views/modules/subscription');

    $sourcePath = __DIR__.'/../Resources/views';

    $this->publishes([
      $sourcePath => $viewPath
    ]);

    $this->loadViewsFrom(array_merge(array_map(function ($path) {
      return $path . '/modules/subscription';
    }, \Config::get('view.paths')), [$sourcePath]), 'subscription');
  }

  /**
  * Register translations.
  *
  * @return void
  */
  public function registerTranslations()
  {
    $langPath = base_path('resources/lang/modules/subscription');

    if (is_dir($langPath)) {
      $this->loadTranslationsFrom($langPath, 'subscription');
    } else {
      $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'subscription');
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
