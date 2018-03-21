## Cache Component
***
PHP7+ cache component that allows more than one cache drivers. Only File and Apc cache drivers are available at the moment.

### Required php version: 7.0+

### Installing:
***
Simply run: composer create-project phoxphp/cache or composer require phoxphp/cache

### Configuration
***
All cache configuration need by this package is in config.php which is in the package's root directory.

### Basic Usage:
***
To start using the cache component, simply instantiate the CacheManager class.

```php
<?php
    $cache = new Kit\Cache\CacheManager();
?>
```

After instatiating the Cache Manager class, you can now start using the cache package.

### Storing a cache:
***
```php
<?php
    $cache->add('cacheKey', 'cacheValue');
?>
```

When storing a cache, we can set the duration that the stored cache will be alive for by passing in the third parameter as done below. Note: the duration is in seconds and it is default to 60 seconds.
```php
<?php
    $cache->add('cacheKey', 'cacheValue', 60);
?>
```

### Reading stored cache:
***
To retrieve or read a stored cache, the get method is used.
```php
<?php
    $cache->get('cacheKey');
?>
```
### Checking if cache exists:
***
To check if cache exists, **exists** method is used.
```php
<?php
    $cache->exists('cacheKey');
?>
```

### Deleting cache:
***
To delete a cache, **delete** method is used.
```php
<?php
    $cache->delete('cacheKey');
?>
```

### Getting date when cache was created:
***

```php
<?php
	$cache->getCreatedDate('cacheKey');
?>
```

### Getting cache expiration date:
***
```php
<?php
	$cache->getExpirationDate('cacheKey');
?>
```

### Checking if cache has expired:
***
Note: If the key is not found associated with any cache stored, it will return false.
```php
<?php
	$cache->hasExpired('cacheKey');
?>
```