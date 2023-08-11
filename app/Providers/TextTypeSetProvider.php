<?php

namespace App\Providers;

use App\Services\TextTypeSetService;
use Illuminate\Support\ServiceProvider;

class TextTypeSetProvider extends ServiceProvider
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
        $this->app->singleton('TextTypeSetService', function($app, $parameters = []){
            return new TextTypeSetService($parameters);
        });
    }
}
