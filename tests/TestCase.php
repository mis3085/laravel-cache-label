<?php

namespace Mis3085\LaravelCacheLabel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mis3085\LaravelCacheLabel\LaravelCacheLabelServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCacheLabelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-cache-label_table.php.stub';
        $migration->up();
        */
    }
}
