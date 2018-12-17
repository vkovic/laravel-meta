# Laravel Meta

[![Build](https://api.travis-ci.org/vkovic/laravel-meta.svg?branch=master)](https://travis-ci.org/vkovic/laravel-meta)
[![Downloads](https://poser.pugx.org/vkovic/laravel-meta/downloads)](https://packagist.org/packages/vkovic/laravel-meta)
[![Stable](https://poser.pugx.org/vkovic/laravel-meta/v/stable)](https://packagist.org/packages/vkovic/laravel-meta)
[![License](https://poser.pugx.org/vkovic/laravel-meta/license)](https://packagist.org/packages/vkovic/laravel-meta)

### Laravel meta storage for different purposes

Easily store and access all kind of metadata for your application in dedicated table.

> The package is one of three metadata packages based on the same approach:
> - vkovic/laravel-meta (this package - general purpose meta storage)
> - [vkovic/laravel-model-meta](https://github.com/vkovic/laravel-model-meta) (Laravel model related meta storage)
> - [vkovic/laravel-settings](https://github.com/vkovic/laravel-settings) (app specific settings meta storage)
>
> Packages can be used separately or together. Internally they are using same table and share common logic.

---

## Compatibility

The package is compatible with Laravel versions `>= 5.5`

## Installation

Install the package via composer:

```bash
composer require vkovic/laravel-meta
```

The package needs to be registered in service providers, so just add it to providers array:

```php
// File: config/app.php

// ...

'providers' => [

    /*
     * Package Service Providers...
     */

    // ...

    Vkovic\LaravelMeta\Providers\LaravelMetaServiceProvider::class,

    // ...
];
```

Run migrations to create table which will be used to store our metadata:

```bash
php artisan migrate
```

### With facade

If you want to use facade (e.g. `Meta::set('foo', 'bar')`) for more intuitive coding, no problem, just register facade in app config file:

```php
// File: config/app.php

// ...

'aliases' => [

    // ...

    'Meta' => \Vkovic\LaravelMeta\Facades\MetaFacade::class,
]
```

### Without facade

If however you don't want to use facade you can still use this package with provided meta handler class.
In this case you do not need to register facade but you need to include `MetaHandler` class.

```php
use Vkovic\LaravelMeta\MetaHandler;

// ...

$meta = new MetaHandler;
$meta->set('foo', 'bar');
```

## Usage: Simple Examples

In examples below we will use facade approach.

Let's create and retrieve some metadata:

```php
// Set meta value as string
Meta::set('foo', 'bar');

// Get meta value
Meta::get('foo')) // : 'bar'

// In case there is no metadata found for given key,
// we can pass default value to return
Meta::get('baz', 'default'); // : 'default'
```

Beside string, metadata can also be stored as integer, float, null, boolean or array:

```php
Meta::set('age', 35);
Meta::set('temperature', 24.7);
Meta::set('value', null);
Meta::set('employed', true);
Meta::set('fruits', ['orange', 'apple']);

Meta::get('age')) // : 35
Meta::get('temperature')) // : 24.7
Meta::get('value', null); // : null
Meta::get('employed'); // : true
Meta::get('fruits', ['orange', 'apple']); // : ['orange', 'apple']
```

We can easily check if meta exists without actually retrieving it from meta table:

```php
Meta::set('foo', 'bar');

Meta::exists('foo'); // : true
```

Counting all meta records is also a breeze:

```php
Meta::set('a', 'one');
Meta::set('b', 'two');

Meta::count(); // : 2
```

If we need all metadata, or just keys, no problem:

```php
Meta::set('a', 'one');
Meta::set('b', 'two');
Meta::set('c', 'three');

// Get all metadata
Meta::all(); // : ['a' => 'one', 'b' => 'two', 'c' => 'three']

// Get only keys
Meta::keys(); // : [0 => 'a', 1 => 'b', 2 => 'c']
```

Also, we can remove meta easily:

```php
Meta::set('a', 'one');
Meta::set('b', 'two');
Meta::set('c', 'three');

// Remove meta by key
Meta::remove('a');

// Or array of keys
Meta::remove(['b', 'c']);
```

If, for some reason, we want to delete all meta at once, no problem:

```php
// This will delete all metadata from our meta table!
Meta::purge();
```