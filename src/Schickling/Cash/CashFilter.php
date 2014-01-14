<?php namespace Schickling\Cash;

use MemcachedInstance;
use Memcached;
use Request;
use Route;

class CashFilter {

    public function filter($route, $request, $response)
    {
        if (Request::getMethod() == 'GET')
        {
            $path = Request::path();
            $content = $response->getContent();
            $routeName = Route::current()->getCompiled()->getStaticPrefix();

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

            // tag cached response
            $tag = MemcachedInstance::get($routeName);
            if ($tag)
            {
                $tag = $tag . ';' . $path;    
            }
            else
            {
                $tag = $path;
            }
            MemcachedInstance::put($routeName, $tag, 0);
        }
    }

}

