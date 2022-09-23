<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        Event::subscribe(CacheEventSubscriber::class);
    }

    public function packageBooted()
    {
        Repository::macro('labels', function ($labels) {
            /** @var \Illuminate\Cache\Repository $this */
            $repositoryClass = config('cache-label.repository');
            $cache = new $repositoryClass(
                $this->getStore(),
                new LabelSet($this->getStore(), (array) $labels)
            );
            /** @phpstan-ignore-next-line */
            if (! is_null($this->getEventDispatcher())) {
                $cache->setEventDispatcher($this->getEventDispatcher());
            }
            return $cache;
        });
    }
}
