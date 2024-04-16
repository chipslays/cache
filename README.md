# 🗄️ Cache

A simple and primitive library for caching values for PHP >8.1.

<p align="center">
    <img src="/.github/static/hero.png">
</p>

## Installation

```bash
composer require please/cache
```

## Examples

You can find usage examples [here](/examples).

## Drivers

Available drivers:
- [Filesystem](#filesystem)
  - This driver uses the file system to store the cache.
- [Memory](#memory)
  - This driver uses the memory to store the cache.
- [Session](#session)
  - This driver uses the `$_SESSION` to store the cache.

### Filesystem

This driver uses the file system to store the cache.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$cache = new Cache(new Filesystem);

$cache->set('foo', 'bar');
$cache->get('foo'); // bar
```

You can provide a specific parameters.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

$driver = new Filesystem(folder: '/path/to/folder', prefix: 'data');

$cache = new Cache($driver);

$cache->set('foo', fn () => ['bar']);
$cache->get('foo'); // ['bar']
```

### Memory

This driver uses the memory to store the cache.

> [!WARNING]
> After the script completes the memory will be cleared.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Memory;

$cache = new Cache(new Memory);
$anotherCacheInstance = new Cache(new Memory);
```

By default, cache created with Memory driver under hood.

```php
use Please\Cache\Cache;

$cache = new Cache;
```

### Session

This driver uses the `$_SESSION` to store the cache.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Session;

$cache = new Cache(new Session);
```

You can pass the key in which the cache will be stored.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Session;

$apiCache = new Cache(new Session('_api'));
$imageCache = new Cache(new Session('_images'));
```

## Cache

You can create as many Cache instances as you need.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Session;
use Please\Cache\Drivers\Filesystem;

$videoCache = new Cache(new Session('your unique key'));
$imageCache = new Cache(new Filesystem('/path/to/images'));
```

By default, for serialization uses native PHP functions `serialize()` and `unserialize()`.

You can create and pass your own serializer if you need to, for example to serialize closures, classes, etc.

```php
use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;
use Please\Cache\Serializers\Contracts\Serializer;

class JsonSerializer implements Serializer
{
    public function serialize(mixed $value): string
    {
        return json_encode($value);
    }

    public function unserialize(mixed $value): mixed
    {
        return json_decode($value, true);
    }
}

$cache = new Cache(new Filesystem, new JsonSerializer);
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
$ttl = strtotime('1 day') - time();
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

> [!NOTE]
> The `$cacheInstance->clear()` method will only work for the instance in which it was called.

```php
$cache->set('foo1', 'bar1')->has('foo1'); // true
$cache->set('foo2', 'bar2')->has('foo2'); // true

$cache->clear();

$cache->has('foo1'); // false
$cache->has('foo2'); // false
```

### forget()

Delete cache by key.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |

```php
$cache->set('foo', 'bar')->has('foo'); // true

$cache->forget('foo');

$cache->has('foo'); // false
```

### pluck()

Removes and returns an item from the cache by its key.

Parameter | Required | Default
--- | --- | ---
`string $key` | `true` |
`mixed $default` | `false` | `null`

```php
$cache->set('foo', 'bar')->has('foo'); // true

$cache->pluck('foo'); // bar

$cache->has('foo'); // false
```

### through()

If the closure is not cached, then executes it, otherwise returns the cached result of closure execution.

This method used [`ClosureHash`](/src/Support/ClosureHash.php) under hood.

> [!NOTE]
> The closure must return a value suitable for serialization by the [serializer](/src/Serializers/) you choose.

Parameter | Required | Default
--- | --- | ---
`string $callback` | `true` |
`int\|string $ttl` | `false` | `1 year`

```php
$closure = function () {
    return mt_rand();
};

$cache->through($closure);
$cache->through($closure); // returns cached result of closure execution
```

## License
Open-sourced software licensed under the [MIT license](https://opensource.org/license/mit/).