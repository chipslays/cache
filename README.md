# 🗄️ Cache

A simple and primitive library for caching values for PHP >8.1.

## Installation

```bash
composer require please/cache
```

## Examples

You can find usage examples [here](/examples).

## Drivers

Currently there is only a `Filesystem` driver, more drivers will be added soon.

### Filesystem

This driver uses the file system to store the cache.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$cache = new Cache(new Filesystem);
```

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$driver = new Filesystem(
    // Where are cache files stored.
    folder: '/path/to/folder',

    // Prefix for cache file.
    prefix: 'cached_',

    // Extension for cache file.
    extension: '.bin',
);

$cache = new Cache($driver);
```

You can create as many Cache instances as you need.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$imageCache = new Cache(new Filesystem('/path/to/images'));
$videoCache = new Cache(new Filesystem('/path/to/videos'));
```

> **NOTE**
> The `$cacheInstance->clear()` method will only work for the instance in which it was called.

## Cache

By default, cache created with Filesystem driver under hood.

```php
use Please\Cache\Cache;

$cache = new Cache;
```

You can pass one of the available drivers.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$cache = new Cache(new Filesystem);
```

By default, for serialization uses native PHP functions `serialize()` and `unserialize()`.

You can create and pass a custom serializer for example, for serializing closures, classes, etc.

```php
use Please\Cache\Cache;

class JsonSerializer extends NativeSerialize
{
    public function serialize(mixed $value): string
    {
        $this->throwExceptionIsNotSerializable($value);

        return json_encode($value);
    }

    public function unserialize(string $value): mixed
    {
        return json_decode($value, true);
    }
}

$cache = new Cache(serializer: new JsonSerializer);
```

## Methods

### set()

Persists value in the cache, uniquely referenced by a key with an optional expiration TTL time.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |
`mixed $value` | `true` |
`int\|string $ttl` | `false` | `1 year`

```php
$cache->set(key: 'foo', value: 'bar', ttl: 3600);
```

You can pass the TTL value as a string like for the `strtotime() ` function.

```php
$cache->set('foo', ['bar', 'baz'], '1 day');

// the example above is equivalent to this code
$ttl = strtotime($ttl) - time();
$cache->set('foo', 'bar', $ttl);
```

### get()

Fetches a value from the cache.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |
`mixed $default ` | `false` | `null`

```php
$cache->get(key: 'foo', default: 'baz');
```

Pass a default value as a `Closure`, it will be executed lazily if the key is not found.

```php
$cache->get('foo', fn () => 'baz');
```

### has()

Determines whether an item is present in the cache.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |

```php
$cache->set('foo', 'bar');

$cache->has('foo'); // true
$cache->has('baz'); // false
```

### clear()

Wipes all cache.

```php
$cache->set('foo1', 'bar1')->has('foo1'); // true
$cache->set('foo2', 'bar2')->has('foo2'); // true

$cache->clear();

$cache->has('foo1'); // false
$cache->has('foo2'); // false
```

### delete()

Delete cache by key.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |

```php
$cache->set('foo', 'bar')->has('foo'); // true

$cache->delete('foo');

$cache->has('foo'); // false
```

## License
Open-sourced software licensed under the [MIT license](https://opensource.org/license/mit/).