<?php

namespace Toropchin\YandexPdd;

use Illuminate\Support\ServiceProvider;

class YandexPddServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/yandexpdd.php' => config_path('yandexpdd.php')], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/yandexpdd.php', 'pdd');

        $this->app->bind('pdd', function () {
            return new Pdd;
        });
    }
}
