# Laravel Meta

[![Build](https://api.travis-ci.org/vkovic/laravel-meta.svg?branch=master)](https://travis-ci.org/vkovic/laravel-meta)
[![Downloads](https://poser.pugx.org/vkovic/laravel-meta/downloads)](https://packagist.org/packages/vkovic/laravel-meta)
[![Stable](https://poser.pugx.org/vkovic/laravel-meta/v/stable)](https://packagist.org/packages/vkovic/laravel-meta)
[![License](https://poser.pugx.org/vkovic/laravel-meta/license)](https://packagist.org/packages/vkovic/laravel-meta)

### Laravel meta storage for different purposes

Easily store and access all kind of meta data for your application in dedicated table.

This can be neat for all kind of global storage for you application, like for example global application settings.
If you need to relate meta data just with your models use (vkovic/laravel-model-meta)[]

---

## Compatibility

The package is compatible with Laravel versions `>= 5.5`

## Installation

Install the package via composer:

```bash
composer require vkovic/laravel-meta
```

The package needs to be registered in service providers:

```php
// File: config/app.php

// ...

/*
 * Package Service Providers...
 */

// ...

Vkovic\LaravelDbRedirector\Providers\DbRedirectorServiceProvider::class,
```

Run migrations to create table which will be used to store our meta data:

```bash
php artisan migrate
```

## Usage: Simple Examples

Writing and reading meta data is easy.
You just use provided `MetaHandler` class and few handy methods it contains.

So, in every file where we want to use meta data functionality,
we should include provided `MetaHandler` class like this:

```php
use Vkovic\LaravelMeta\MetaHandler;
```

Now we have everything we need to start using this package.

Let's create and retrieve some meta data:

```php
$meta = new MetaHandler;

// Set value by key
$meta->set('someKey', 'someValue');

// Get value by key
dd($meta->get('someKey'));

// Result: 'someValue'

// In case there is no meta data found for givven key,
// we can pass default value to return
dd($meta->get('unexistingKey', 'foo'));

// Result: 'foo'
```

We can easily check if meta exists without actually retrieving it from meta table:

```php
$meta = new MetaHandler;

$meta->set('someKey', 'someValue');

$meta->exists('someKey');

// Result: true

dd($meta->exists('unexistingKey'));

// Result: false
```

Counting meta is also a breeze:

```php
$meta = new MetaHandler;

$meta->set('someKey', 'someValue');
$meta->set('foo', 'bar');

dd($meta->count());
// Result: 2
```

If we need all meta data, or just keys, no problem:

```php
$meta = new MetaHandler;

$meta->set('someKey', 'someValue');
$meta->set('foo', 'bar');
$meta->set('one', 'two');

// Get all meta data
dd($meta->all());

// Result:
// [
//     'someKey' => 'someValue',
//     'foo' => 'bar',
//     'one' => 'two',
// ]

// Get only keys
dd($meta->keys());

// Result:
// [
//     0 => 'someKey',
//     1 => 'foo'
//     2 => 'one'
// ]
```

Also, we can remove meta easily:

```php
$meta = new MetaHandler;

$meta->set('someKey', 'someValue');
$meta->set('foo', 'bar');
$meta->set('one', 'two');

// Remove meta by key
$meta->remove('someKey');

// Or array of keys
$meta->remove(['foo', 'one']);
```

If, for some reason, we want to delete all meta at once, no problem:

```php
$meta = new MetaHandler;

// Purge meta. This will delete all meta data from our meta table!
$meta->purge();
```