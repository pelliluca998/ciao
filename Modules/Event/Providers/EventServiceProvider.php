<?php

namespace Modules\Event\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class EventServiceProvider extends ServiceProvider
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
	   $menuList->add("Eventi", array("route" => "events.index"))
	   		->prepend("<i class='fa fa-calendar' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all', 'mod_elenco'])->data('order', 20);
	   
	   $menuList->get('eventi')
	   		->add('Eventi', array('route'  => 'events.index'))
	   		->prepend("<i class='fa fa-calendar' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 21);
	   		
	   $menuList->get('eventi')
	   		->add('Settimane', array('route'  => 'week.index'))
	   		->prepend("<i class='fa fa-sun-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 22);
	   		
	   $menuList->get('eventi')
	   		->add('Strumenti', array('route'  => 'events.strumenti'))
	   		->prepend("<i class='fa fa-sun-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 22);
	   		
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
            __DIR__.'/../Config/config.php' => config_path('event.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'event'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/event');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/event';
        }, \Config::get('view.paths')), [$sourcePath]), 'event');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/event');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'event');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'event');
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
