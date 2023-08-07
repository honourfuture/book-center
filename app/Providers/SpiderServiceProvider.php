<?php

namespace App\Providers;

use App\Services\SpiderService;
use Illuminate\Support\ServiceProvider;

class SpiderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('SpiderService', function($app, $parameters = []){
            return new SpiderService($parameters);
        });
    }
}
