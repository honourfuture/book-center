<?php

namespace App\Providers;

use App\Services\ExcellentArticleService;
use Illuminate\Support\ServiceProvider;

class ExcellentArticleServiceProvider extends ServiceProvider
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
        $this->app->singleton('ExcellentArticleService', function($app){
            return new ExcellentArticleService();
        });
    }
}
