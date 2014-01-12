<?php namespace Schickling\Cash;

use Illuminate\Support\ServiceProvider;
use Route;
use App;

class CashServiceProvider extends ServiceProvider {

    protected $cash;

    public function __construct($app) {
        parent::__construct($app);
        $this->cash = new Cash;
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Route::filter('cash', 'Schickling\Cash\CashFilter');

        $cash = $this->cash;
        App::after(function($request, $response) use ($cash)
        {
            $cash->checkInvalidation();
        });
    }

    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $cash = $this->cash;
        $this->app['cash'] = $this->app->share(function($app) use ($cash)
        {
            return $cash;
        });

        // Shortcut so developers don't need to add an alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Cash', 'Schickling\Cash\Facades\Cash');
        });
    }

}
