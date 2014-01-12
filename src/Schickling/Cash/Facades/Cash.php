<?php namespace Schickling\Cash;

use Cache;
use Memcached;
use Request;

class CashFilter {

    public function filter($route, $request, $response)
    {
        if (Request::getMethod() == 'GET')
        {
            $path = '/' . Request::path();
            $content = $response->getContent();
            $cache = Cache::driver('memcached');
            $memcached = $cache->getMemcached();
            $memcached->setOption(Memcached::OPT_COMPRESSION, false);
            $cache->put($path, $content, 0);
        }
    }

}

