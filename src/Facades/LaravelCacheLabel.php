<?php

namespace Mis3085\LaravelCacheLabel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mis3085\LaravelCacheLabel\LaravelCacheLabel
 */
class LaravelCacheLabel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Mis3085\LaravelCacheLabel\LaravelCacheLabel::class;
    }
}
