<?php

namespace Vkovic\LaravelMeta\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelMetaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config' => config_path()
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-meta.php', 'laravel-meta');
    }
}
