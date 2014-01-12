<?php namespace Services\Cash;

use Illuminate\Support\ServiceProvider;
use Route;

class CashServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Route::filter('cash', 'Services\Cash\CashFilter');
    }

    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {

        $this->app['apicache'] = $this->app->share(function($app)
        {
            return new Cash;
        });

        // Shortcut so developers don't need to add an alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Cash', 'Services\Cash\Facades\Cash');
        });
    }

}
