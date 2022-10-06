<?php

namespace Mis3085\LaravelCacheLabel;

trait CacheLabelTrait
{
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
