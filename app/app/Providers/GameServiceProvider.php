<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GameService;

class GameServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GameService::class, function(){
            return new GameService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
