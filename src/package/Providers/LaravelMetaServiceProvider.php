<?php

namespace Vkovic\LaravelMeta\Providers;

use Illuminate\Support\ServiceProvider;
use Vkovic\LaravelMeta\MetaHandler;
use Vkovic\LaravelMeta\Models\Meta;

class LaravelMetaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->app->singleton('vkovic.laravel-meta', function () {
            return new MetaHandler(new Meta);
        });
    }
}
