laravel-cash [![Build Status](https://travis-ci.org/schickling/laravel-cash.png?branch=master)](https://travis-ci.org/schickling/laravel-cash) [![Coverage Status](https://coveralls.io/repos/schickling/laravel-cash/badge.png)](https://coveralls.io/r/schickling/laravel-cash) [![Total Downloads](https://poser.pugx.org/schickling/laravel-cash/downloads.png)](https://packagist.org/packages/schickling/laravel-cash)
============

Simple to use cache layer for your laravel application using memcached & nginx. ~400% more performance.

#### Warning: Still in development. First release coming soon!

## Installation

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

upstream backend {
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
            memcached_pass memcached;
            error_page 404 502 = @nocache;
        }
        if ($request_method != GET) {
            fastcgi_pass backend;
        }
    }

    location @nocache {
        fastcgi_pass backend;
    }
}
```

## Usage

### Add cache filter to routes

### Define invalidation roules

### Flush cache
Simply restart memcached.


## TODO

* Support for named routes
* More precise hierarchic invalidation
