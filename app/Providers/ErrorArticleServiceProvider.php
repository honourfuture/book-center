<?php

namespace App\Providers;

use App\Services\ErrorArticleService;
use Illuminate\Support\ServiceProvider;

class ErrorArticleServiceProvider extends ServiceProvider
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
        $this->app->singleton('ErrorArticleService', function($app){
            return new ErrorArticleService();
        });
    }
}
