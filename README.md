laravel-cash [![Build Status](https://travis-ci.org/schickling/laravel-cash.png?branch=master)](https://travis-ci.org/schickling/laravel-cash) [![Coverage Status](https://coveralls.io/repos/schickling/laravel-cash/badge.png)](https://coveralls.io/r/schickling/laravel-cash) [![Total Downloads](https://poser.pugx.org/schickling/laravel-cash/downloads.png)](https://packagist.org/packages/schickling/laravel-cash)
============

Simple to use cache layer for your laravel application using memcached & nginx. ~400% faster response times.

## How it works

The packages caches the responses to `GET` requests in memcached using the URL as key. Any further requests get served the cached content by nginx directly without running PHP. Writing actions can easily invalidate the cache.

### Features

* Easy to setup and use
* Self defined invalidation rules
* Automatic cache refilling
* Cache warmup

### Dependencies
* Laravel 4.1
* nginx ([installation guide](https://github.com/schickling/laravel-cash/blob/master/doc/NGINX.md))
* memcached && PHP memcached extension ([installation guide](https://github.com/schickling/laravel-cash/blob/master/doc/MEMCACHED.md))

## Quick setup

1. Add the following to your composer.json and run `composer update`

    ```json
    {
        "require": {
            "schickling/laravel-cash": "dev-master"
        }
    }
    ```

2. Add `Schickling\Cash\CashServiceProvider` to your config/app.php

3. Ajust your nginx vhost ([more configurations](https://github.com/schickling/laravel-cash/blob/master/doc/NGINX.md))

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

### Define invalidation rules

Add rules of the following syntax in your `routes.php` file. The `$routeToInvalidate` parameter may be a string or an array and describe always `GET` routes.
```php
Cash::rule($httpMethod, $triggerRoute, $routesToInvalidate);
```

Let's say you have a cached `GET users` route to retrieve all users and a `POST users` route to create a new user. Your goal is to invalidate the `GET users` cache if a new user was created.

```php
Cash::rule('POST', 'users', 'users');
```

##### Multiple route caches
```php
Cash::rule('POST', 'users', array('users', 'premium/users'));
```

##### Dynamic rules
```php
Cash::rule('POST', 'users', 'users/*');
```

### Flush cache
Simply restart memcached.


## Coming soon (please contribute a [pull request](https://github.com/schickling/laravel-cash/compare/))

* Support for named routes
* Support for rules in route group scope
* More precise hierarchic invalidation
* Apache support
* Memcache support
* Cache warmup
* Individual cache rewarmup
