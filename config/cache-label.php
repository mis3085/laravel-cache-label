<?php

// config for Mis3085/LaravelCacheLabel
return [
    /**
     * Prefix key of every label cache
     */
    'prefix' => 'label:',

    /**
     * Affix key of every label cache
     */
    'affix' => ':key',

    /**
     * The default cache ttl of a label, null means forever.
     */
    'ttl' => null,

    /**
     * The repository class to replace the regular cache repository.
     */
    'repository' => \Mis3085\LaravelCacheLabel\CacheLabelRepository::class,

    /**
     * The behavior when calling flush()
     *
     * 'flush_labels' => clear all items of the labels, but still remmember their keys for further use.
     * 'reset_labels' => only unlink the relation between items and the label, but does not touch the value of items.
     * default => refer to the flush() of current cache store which will wipe out everything.
     *
     */
    'flush_behavior' => null,

    /**
     * The behavior when calling forget()
     *
     * true => forget the item and also remove the key from the labels.
     * false => only forget the item from cache store, but does not remove its key from the labels.
     */
    'forget_and_remove' => false,
];
