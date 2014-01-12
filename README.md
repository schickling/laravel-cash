laravel-cash [![Build Status](https://travis-ci.org/schickling/laravel-cash.png?branch=master)](https://travis-ci.org/schickling/laravel-cash)
============

Simple to use cache layer for your laravel application using memcached.

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

## Usage

### Add cache filter to routes

### Define invalidation roules

### Flush cache
Simply restart memcached.