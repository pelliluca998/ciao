<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class UserServiceProvider extends ServiceProvider
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
        $menuList->add('Il tuo profilo',array('route'  => 'profile.show'))
        		->prepend("<i class='fa fa-address-card-o' aria-hidden='true'></i> ")
        		->data('permissions', ['usermodule', 'all'])->data('order', 1)
        		->nickname('profilo');
        $menuList->get('profilo')
	   		->add('Il tuo profilo', array('route'  => 'profile.show'))
	   		->prepend("<i class='fa fa-address-card-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['usermodule', 'all'])->data('order', 2);
	   $menuList->get('profilo')
	   		->add('Affiliazione oratorio', array('route'  => 'oratorio.affiliazione'))
	   		->prepend("<i class='fa fa-cubes' aria-hidden='true'></i> ")
	   		->data('permissions', ['usermodule', 'all'])->data('order', 2);
	   $menuList->add("Anagrafica", array("route" => "user.index"))
	   		->prepend("<i class='fa fa-user' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 2);
	   $menuList->get('anagrafica')
	   		->add('Anagrafica', array('route'  => 'user.index'))
	   		->prepend("<i class='fa fa-user' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 3);
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
            __DIR__.'/../Config/config.php' => config_path('user.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'user'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/user');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/user';
        }, \Config::get('view.paths')), [$sourcePath]), 'user');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/user');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'user');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'user');
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
