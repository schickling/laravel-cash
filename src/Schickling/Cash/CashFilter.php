<?php namespace Schickling\Cash;

use MemcachedInstance;
use Memcached;
use Request;

class CashFilter {

    public function filter($route, $request, $response)
    {
        if (Request::getMethod() == 'GET')
        {
            $path = '/' . Request::path();
            $content = $response->getContent();

            // switch of serialization
            $memcached = MemcachedInstance::getMemcached();
            $memcached->setOption(Memcached::OPT_COMPRESSION, false);

            // cache response
            MemcachedInstance::put($path, $content, 0);
        }
    }

}

