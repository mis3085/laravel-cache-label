# An alternative method of categorizing cached items in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mis3085/laravel-cache-label.svg?style=flat-square)](https://packagist.org/packages/mis3085/laravel-cache-label)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/mis3085/laravel-cache-label/run-tests?label=tests)](https://github.com/mis3085/laravel-cache-label/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/mis3085/laravel-cache-label/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/mis3085/laravel-cache-label/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mis3085/laravel-cache-label.svg?style=flat-square)](https://packagist.org/packages/mis3085/laravel-cache-label)

This package is a complementary use of Laravel Cache, developed based on the syntax of Cache Tag.

Cache Tag creates separate namespaces for different tag combinations to store cache items. A cache item named FOOBAR can exist in different tag namespaces and have different values.

Cache Label only establishes the reference relationship between labels and cache items. A cache item named FOOBAR will be the only item in the entire cache store.

## Installation

You can install the package via composer:

```bash
composer require mis3085/laravel-cache-label
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-cache-label-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

### Storing labeled cache items

```php
cache()->labels(['people', 'artists'])->put('John', $john, $seconds);
cache()->labels(['people', 'authors'])->put('Anne', $anne, $seconds);
```

### Accessing labeled cache items

The following statements return the same value.

```php
cache()->labels(['people', 'artists'])->get('John');
cache()->labels(['artists', 'people'])->get('John');
cache()->labels('people')->get('John');
cache()->labels('artists')->get('John');
cache()->get('John');
```

The cache key must exist in each label, otherwise the empty or default value will be returned.

```php
cache()->labels(['authors', 'people'])->get('John');
// null, John does not belong to authors
```

### Flushing labeled cache items

Clear the value of items associated with the labels, but still remember the cache keys for further use.

```php
cache()->labels('artists')->flushLabels();

cache()->get('John');
// null, been wiped by the "artists" label
```

```php
cache()->put('John', 'john-updated-1', $seconds);
cache()->get('John');
// "john-updated-1"

cache()->labels('people')->flushLabels();

cache()->get('John');
// null, been wiped by the "people" label

cache()->get('Anne');
// null, also been wiped
```

### Resetting labels

Let the label no longer track any cache items, but does not clear the values of its previous associated cache items.

```php
cache()->put('John', 'john-updated-2', $seconds);

cache()->labels('artists')->resetLabels();
// No longer track any cache items

cache()->labels('artists')->flushLabels();
cache()->get('John');
// "john-updated-2"
```

### Calling forget() with labels()

When forget() is called with labels(), the value of the cache item is cleared, but the label does not forget the key of the cache item. This behavior can be changed by modifying config('cache-label.forget_and_remove'). When set to true, the label will forget the cache key after its value has been cleared.

#### By default

```php
cache()->labels(['people', 'artists'])->put('John', $john, $seconds);
cache()->labels('artists')->forget('John');
cache()->get('John');
// null

cache()->put('John', 'john-updated-1', $seconds);
cache()->labels('artists')->get('John');
// "john-updated-1", the "artists" label still can access "John"
```

#### Setting config('cache-label.forget_and_remove') to true

```php
cache()->labels(['people', 'artists'])->put('John', $john, $seconds);
cache()->labels('artists')->forget('John');
cache()->get('John');
// null

cache()->put('John', 'john-updated-1', $seconds);

cache()->labels('artists')->get('John');
// null, the "artists" label can no longer access "John"

cache()->labels('people')->get('John');
// "john-updated-1", the "people" label is unaffected
```

### Calling flush() with labels()

When flush() is called, all items in the entire cache store will be flushed, regardless of whether labels() is used or not. This behavior can be changed by modifying config('cache-label.flush_behavior'). However, this is not recommended, and it is better to use flushLabels() or resetLabels() to manipulate the labeled items.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
