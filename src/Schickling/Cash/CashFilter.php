<?php namespace Schickling\Cash;

use MemcachedInstance;
use Memcached;
use Request;

class CashFilter {

    public function filter($route, $request, $response)
    {
        if (Request::getMethod() == 'GET')
        {
            $path = Request::path();
            $content = $response->getContent();

            // prepend slash if not there already
            if (substr($path, 0, 1) != '/')
            {
                $path = '/' . $path;
            }

            // switch of serialization
            $memcached = MemcachedInstance::getMemcached();
            $memcached->setOption(Memcached::OPT_COMPRESSION, false);

            // cache response
            MemcachedInstance::put($path, $content, 0);
        }
    }

}

