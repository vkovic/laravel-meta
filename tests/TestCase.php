<?php

namespace Vkovic\LaravelMeta\Test;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vkovic\LaravelMeta\Providers\LaravelMetaServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Default table
     *
     * @var string
     */
    protected $table;

    /**
     * Default realm
     *
     * @var string
     */
    protected $realm;

    /**
     * Setup the test environment.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Retrieve table and default realm name for later use
        $this->table = config('laravel-meta.table_name');
        $this->realm = config('laravel-meta.realm');

        $this->packageMigrations();
    }

    /**
     * Run default package migrations
     *
     * @return void
     */
    protected function packageMigrations()
    {
        $this->artisan('migrate');
    }

    /**
     * Get package providers
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelMetaServiceProvider::class];
    }

    /**
     * Define environment setup
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }
}