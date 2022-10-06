<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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

            // When using the default repository and version of Laravel >= 9, replace it with v9 repository.
            if (version_compare(app()->version(), '9.0.0') >= 0 && Str::startsWith($repositoryClass, __NAMESPACE__)) {
                $repositoryClass = CacheLabelRepositoryV9::class;
            }

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
