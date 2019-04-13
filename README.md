# Laravel Meta

[![Build](https://api.travis-ci.org/vkovic/laravel-meta.svg?branch=master)](https://travis-ci.org/vkovic/laravel-meta)
[![Downloads](https://poser.pugx.org/vkovic/laravel-meta/downloads)](https://packagist.org/packages/vkovic/laravel-meta)
[![Stable](https://poser.pugx.org/vkovic/laravel-meta/v/stable)](https://packagist.org/packages/vkovic/laravel-meta)
[![License](https://poser.pugx.org/vkovic/laravel-meta/license)](https://packagist.org/packages/vkovic/laravel-meta)

---

## Compatibility

The package is compatible with Laravel versions `>= 5.5`

## Installation

Install the package via composer:

```bash
composer require vkovic/laravel-meta
```

Run migrations to create table which will be used to store our metadata:

```bash
php artisan migrate
```

## Usage

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

Multiple records could be retrieved using `query` method and wildcard `*`:

```php
Meta::set('settings.display.resolution', '1280x1024');
Meta::set('settings.display.brightness', 97);
Meta::set('settings.sound.volume', 54);
Meta::set('settings.mic.volume', 0);

Meta::query('settings.display.*');
// Result:
// [
//     'settings.display.resolution' => '1280x1024',
//     'settings.display.brightness' => 97
// ]

Meta::query('*.sound.*');
// Result:
// [
//     'settings.sound.volume' => 54
// ]

Meta::query('settings.*.volume');
// Result:
// [
//     'settings.sound.volume' => 54,
//     'settings.mic.volume' => 0
// ]

// In case there is no metadata found for given query,
// we can pass default value to return
Meta::query('settings.sound.bass', 85); // : 85
```

Beside string, metadata can also be stored as integer, float, null, boolean or array:

```php
Meta::set('age', 35);
Meta::set('temperature', 24.7);
Meta::set('value', null);
Meta::set('employed', true);
Meta::set('fruits', ['orange', 'apple']);

Meta::get('age'); // : 35
Meta::get('temperature'); // : 24.7
Meta::get('value'); // : null
Meta::get('employed'); // : true
Meta::get('fruits'); // : ['orange', 'apple']
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

If we need to access underlying meta model (Laravel Eloquent Model) to manipulate or retrieve data with unlimited control we can get it like this:

```php
Meta::getModel();
```

---

### Laravel meta storage for different purposes

Easily store and access all kind of metadata for your application in dedicated table.

> The package is one of three metadata packages based on the same approach:
> - vkovic/laravel-meta (this package - general purpose meta storage)
> - [vkovic/laravel-model-meta](https://github.com/vkovic/laravel-model-meta) (Laravel model related meta storage)
> - [vkovic/laravel-settings](https://github.com/vkovic/laravel-settings) (app specific settings meta storage)
>
> Packages can be used separately or together. Internally they are using same table and share common logic.