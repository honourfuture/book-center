<?php

namespace App\Providers;

use App\Services\BaiduTjService;
use Illuminate\Support\ServiceProvider;

class BaiduTjServiceProvider extends ServiceProvider
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
        $this->app->singleton('BaiduTjService', function($app){
            return new BaiduTjService();
        });
    }
}
