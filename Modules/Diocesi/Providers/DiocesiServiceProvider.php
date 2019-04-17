<?php

namespace Modules\Diocesi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class DiocesiServiceProvider extends ServiceProvider
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
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->app['router']->aliasMiddleware('role', \Zizaco\Entrust\Middleware\EntrustRole::class);
        $this->app['router']->aliasMiddleware('license', \App\Http\Middleware\CheckLicense::class);
        //Popolo il menu con un link a questo modulo.
        //Il menu è stato definito in app/Provider/AppServiceProvider.php
        $menuList = Menu::get('SegrestaNavBar');
        $menuList->add("Diocesi", array("route" => "oratori.index"))
        ->prepend("<i class='fas fa-church' aria-hidden='true'></i> ")
        ->data('permissions', ['edit-oratori', 'view-users-diocesi'])->data('order', 90);

        $menuList->get('diocesi')
        ->add('Oratori', array('route'  => 'oratori.index'))
        ->prepend("<i class='fas fa-ring' aria-hidden='true'></i> ")
        ->data('permissions', ['edit-oratori'])->data('order', 91);
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
            __DIR__.'/../Config/config.php' => config_path('diocesi.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'diocesi'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/diocesi');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/diocesi';
        }, \Config::get('view.paths')), [$sourcePath]), 'diocesi');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/diocesi');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'diocesi');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'diocesi');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
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
