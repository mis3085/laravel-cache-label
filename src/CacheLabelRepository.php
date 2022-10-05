<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;

class CacheLabelRepository extends Repository
{
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
    public function get($key, $default = null)
    {
        $value = parent::get($key, $default);

        if (! $this->labels->hasItemKey($key)) {
            return value($default);
        }

        return $value;
    }

    public function flush()
    {
        switch (config('cache-label.flush_behavior')) {
            case 'flush_labels':
                return $this->flushLabels();
            case 'reset_labels':
                return $this->resetLabels();
            default:
                return $this->clear();
        }
    }

    /**
     * Flush all items of the labels in the set.
     *
     * @return bool
     */
    public function flushLabels()
    {
        return $this->labels->flush();
    }

    /**
     * Reset all labels in the set.
     *
     * @return bool
     */
    public function resetLabels()
    {
        return $this->labels->reset();
    }

    /**
     * {@inheritDoc}
     */
    protected function event($event)
    {
        /** @var \Illuminate\Cache\Events\CacheEvent $event */
        parent::event($event->setTags($this->labels->getLabelKeys()));
    }

    /**
     * Get the label set instance.
     *
     * @return LabelSet
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Unlink specified keys from the labels
     *
     * @param  string|array $keys
     * @return bool
     */
    public function unlinkItems($keys)
    {
        return $this->labels->unlinkItems($keys);
    }
}
