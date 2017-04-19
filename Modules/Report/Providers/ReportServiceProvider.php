<?php

namespace Modules\Report\Providers;

use Illuminate\Support\ServiceProvider;
use Menu;
use Entrust;
use Illuminate\Support\Facades\Auth;

class ReportServiceProvider extends ServiceProvider
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
	   $menuList->add("Report", array("route" => "report.eventspec"))
	   		->prepend("<i class='fa fa-file-text-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 40);
	   
	   $menuList->get('report')
	   		->add('Report Specifiche evento', array('route'  => 'report.eventspec'))
	   		->prepend("<i class='fa fa-file-text-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 41);
	   		
	   $menuList->get('report')
	   		->add('Report Specifiche settimane', array('route'  => 'report.weekspec'))
	   		->prepend("<i class='fa fa-file-text-o' aria-hidden='true'></i> ")
	   		->data('permissions', ['adminmodule', 'all'])->data('order', 42);
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
            __DIR__.'/../Config/config.php' => config_path('report.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'report'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/report');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/report';
        }, \Config::get('view.paths')), [$sourcePath]), 'report');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/report');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'report');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'report');
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
