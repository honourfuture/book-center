<?php

namespace App\Providers;

use App\Services\OverviewUrlService;
use Illuminate\Support\ServiceProvider;

class OverviewUrlServiceProvider extends ServiceProvider
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
        $this->app->singleton('OverviewUrlService', function($app){
            return new OverviewUrlService();
        });
    }
}
