<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;

/**
 * Repository for Laravel 9, to compatible with the strict return type of get().
 */
class CacheLabelRepositoryV9 extends Repository
{
    use CacheLabelTrait;

    /**
     * Label set
     *
     * @var LabelSet
     */
    public $labels;

    /**
     * Create a new cache repository instance.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  LabelSet $labels
     * @return void
     */
    public function __construct(Store $store, LabelSet $labels)
    {
        parent::__construct($store);

        $this->labels = $labels;
    }

    /**
     * {@inheritDoc}
     *
     * The $key has to exist in all of the labels, or the default value would been returned.
     */
    public function get($key, $default = null): mixed
    {
        $value = parent::get($key, $default);

        if (! $this->labels->hasItemKey($key)) {
            return value($default);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function event($event)
    {
        /** @var \Illuminate\Cache\Events\CacheEvent $event */
        parent::event($event->setTags($this->labels->getLabelKeys()));
    }
}
