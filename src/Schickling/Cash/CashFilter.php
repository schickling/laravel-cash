<?php namespace Schickling\Cash;

use MemcachedDriver;
use Request;
use Route;

class CashFilter {

    public function filter($route, $request, $response)
    {
        if (Request::getMethod() == 'GET')
        {
            $path = Request::path();
            $secondsToLife = 0;
            $content = $response->getContent();

            // prepend slash if not there already (required by nginx)
            if (substr($path, 0, 1) != '/')
            {
                $path = '/' . $path;
            }

            // cache response
            MemcachedDriver::put($path, $content, $secondsToLife);

            // alreadyTaggedRoutes cached response
            $staticRouteName = Route::current()->getCompiled()->getStaticPrefix();
            $alreadyTaggedRoutes = MemcachedDriver::get('tag:' . $staticRouteName);
            if ($alreadyTaggedRoutes)
            {
                $alreadyTaggedRoutes = $alreadyTaggedRoutes . ';' . $path;    
            }
            else
            {
                $alreadyTaggedRoutes = $path;
            }
            MemcachedDriver::put($staticRouteName, $alreadyTaggedRoutes, 0);
        }
    }

}

