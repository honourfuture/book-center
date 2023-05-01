<?php

namespace App\Providers;

use App\Services\HttpProxyService;
use Illuminate\Support\ServiceProvider;

class HttpProxyServiceProvider extends ServiceProvider
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
        $this->app->singleton('HttpProxyService', function($app){
            return new HttpProxyService();
        });
    }
}
