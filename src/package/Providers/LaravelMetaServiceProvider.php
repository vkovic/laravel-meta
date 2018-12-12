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
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
