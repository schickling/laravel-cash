laravel-cash [![Build Status](https://travis-ci.org/schickling/laravel-cash.png?branch=master)](https://travis-ci.org/schickling/laravel-cash) [![Coverage Status](https://coveralls.io/repos/schickling/laravel-cash/badge.png)](https://coveralls.io/r/schickling/laravel-cash) [![Total Downloads](https://poser.pugx.org/schickling/laravel-cash/downloads.png)](https://packagist.org/packages/schickling/laravel-cash)
============

Simple to use cache layer for your laravel application using memcached & nginx. Up to ~400% faster respons times.

## How it works

Your application caches the responses to GET requests to memcached using the Request-URI as key. Following requests get served this content by nginx directly from memcached, php/laravel is never even hit. Writing actions can easily invalidate the cache.

## Installation - Laravel 4.1 required

1. If you don't have memcached already installed, follow this [guide](https://github.com/schickling/laravel-cash/blob/master/doc/MEMCACHED.md).

2. Add the following to your composer.json and run `composer update`

    ```json
    {
        "require": {
            "schickling/laravel-cash": "dev-master"
        }
    }
    ```

3. Add `Schickling\Cash\CashServiceProvider` to your config/app.php

4. Ajust your nginx vhost

```nginx
upstream memcached {
    server 127.0.0.1:11211;
    keepalive 1024;
}

upstream laravel {
    server 127.0.0.1:9999;
}

server {
    listen *:80;
    server_name myapp.dev;

    root /path/to/your/public;
    index index.php;

    rewrite ^/(.*)$ /index.php?/$1 last;

    location ~ \.php$ {
        default_type "application/json";
        if ($request_method = GET) {
            set $memcached_key laravel:$request_uri;
            memcached_pass laravel;
            error_page 404 502 = @nocache;
        }
        if ($request_method != GET) {
            fastcgi_pass laravel;
        }
    }

    location @nocache {
        fastcgi_pass laravel;
    }
}
```

## Usage

### Add cache filter to routes
Add the `'after' => 'cash'` filter to `GET` routes you want to be cached. Works also for groups of routes.

```php
Route::get('users', array('after' => 'cash', function()
{
	return User::all();
}));
```

### Define invalidation roules

Add rules of the following syntax in your `routes.php` file. The `$routeToInvalidate` parameter may be a string or an array and describe always `GET` routes.
```php
Cash::rule($httpMethod, $triggerRoute, $routesToInvalidate);
```

Let's say you have a cached `GET users` route to retrieve all users and a `POST users` route to create a new user. Your goal is to invalidate the `GET users` cache if a new user was created.

```php
Cash::rule('POST', 'users', 'users');
```

#### Multiple route caches
```php
Cash::rule('POST', 'users', array('users', 'premium/users'));
```

#### Dynamic rules
```php
Cash::rule('POST', 'users', 'users/*');
```

### Flush cache
Simply restart memcached.


## TODO (please contribute a [pull request](https://github.com/schickling/laravel-cash/compare/))

* Support for named routes
* More precise hierarchic invalidation
* Apache support
* Memcache support
* Cache warmup
* Individual cache rewarmup
