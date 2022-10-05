<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CacheEventSubscriber
{
    public function handleKeyForgotten(KeyForgotten $event)
    {
        if (! $event->tags || ! config('cache-label.forget_and_remove')) {
            return;
        }
        foreach ($event->tags as $label) {
            if (! $this->isValidLabel($label)) {
                continue;
            }
            $currentKeys = cache()->get($label, []);

            cache()->put($label, Arr::except($currentKeys, $event->key), config('cache-label.ttl'));
        }
    }

    public function handleKeyWritten(KeyWritten $event)
    {
        if (! $event->tags) {
            return;
        }

        foreach ($event->tags as $label) {
            if (! $this->isValidLabel($label)) {
                continue;
            }
            $currentKeys = cache()->get($label, []);
            $currentKeys[$event->key] = true;
            cache()->put($label, $currentKeys, config('cache-label.ttl'));
        }
    }

    protected function isValidLabel(string $label): bool
    {
        return Str::is(config('cache-label.prefix') . '*' . config('cache-label.affix'), $label);
    }

    public function subscribe($events)
    {
        return [
            KeyForgotten::class => 'handleKeyForgotten',
            KeyWritten::class => 'handleKeyWritten',
        ];
    }
}
