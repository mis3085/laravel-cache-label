<?php

namespace Mis3085\LaravelCacheLabel;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Arr;

class LabelSet
{
    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    protected $store;

    /**
     * The label names.
     *
     * @var array
     */
    protected $names = [];

    public function __construct(Store $store, array $names = [])
    {
        $this->store = $store;
        $this->names = $names;
    }

    /**
     * Reset all labels in the set.
     *
     * @return bool
     */
    public function reset()
    {
        array_walk($this->names, [$this, 'resetLabel']);
        return true;
    }

    /**
     * Reset the label back to empty array.
     *
     * @param  string $name
     * @return void
     */
    public function resetLabel(string $name)
    {
        $this->store->put($this->labelKey($name), [], config('cache-label.ttl') ?? 0);
    }

    /**
     * Flush all items of the labels in the set.
     *
     * @return bool
     */
    public function flush()
    {
        array_walk($this->names, [$this, 'flushLabel']);
        return true;
    }

    /**
     * Flush the items of the label from the cache.
     *
     * @param  string  $name
     */
    public function flushLabel(string $name)
    {
        $itemKeys = (array) $this->store->get($this->labelKey($name));
        foreach ($itemKeys as $key => $value) {
            $this->store->forget($key);
        }
    }

    /**
     * Whether the itemKey exists in every label
     *
     * @param  mixed   $itemKey
     * @return boolean
     */
    public function hasItemKey($itemKey): bool
    {
        foreach ($this->getLabelKeys() as $labelKey) {
            $stored = (array) $this->store->get($labelKey);
            if (! Arr::has($stored, $itemKey)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get item keys of label
     *
     * @param  string $name
     * @return array
     */
    public function getItemKeysOfLabel(string $name): array
    {
        return array_keys((array) $this->store->get($this->labelKey($name)));
    }

    /**
     * Unlink specified keys from the labels
     *
     * @param  string|array $itemKeys
     * @return bool
     */
    public function unlinkItems($itemKeys): bool
    {
        $result = true;
        foreach ($this->getLabelKeys() as $labelKey) {
            $stored = (array) $this->store->get($labelKey);

            $newValue = Arr::except($stored, $itemKeys);

            $response = config('cache-label.ttl')
                ? $this->store->put($labelKey, $newValue, config('cache-label.ttl'))
                : $this->store->forever($labelKey, $newValue);

            if (! $response) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Get the label identifier key for a given label.
     *
     * @param  string  $name
     * @return string
     */
    public function labelKey($name)
    {
        return config('cache-label.prefix') . $name . config('cache-label.affix');
    }

    /**
     * Get all of the label names in the set.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Get all of the label keys
     *
     * @return array
     */
    public function getLabelKeys()
    {
        return array_map([$this, 'labelKey'], $this->names);
    }
}
