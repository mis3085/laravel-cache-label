<?php

namespace Mis3085\LaravelCacheLabel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Mis3085\LaravelCacheLabel\Commands\LaravelCacheLabelCommand;

class LaravelCacheLabelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cache-label')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-cache-label_table')
            ->hasCommand(LaravelCacheLabelCommand::class);
    }
}
