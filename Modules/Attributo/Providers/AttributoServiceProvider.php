<?php

namespace Modules\Attributo\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class AttributoServiceProvider extends ServiceProvider
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
	   $menuList->get('anagrafica')
	   		->add('Attributi', array('route'  => 'attributo.index'))
	   		->prepend("<i class='fa fa-paperclip' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 5);
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
            __DIR__.'/../Config/config.php' => config_path('attributo.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'attributo'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/attributo');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/attributo';
        }, \Config::get('view.paths')), [$sourcePath]), 'attributo');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/attributo');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'attributo');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'attributo');
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
