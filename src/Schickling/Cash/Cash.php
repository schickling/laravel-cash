<?php namespace Schickling\Cash;

use App;
use Request;
use Illuminate\Routing\Route;
use MemcachedInstance;

class Cash
{

    protected $rules = array();

    public function rule($method, $triggerRoute, $invalidationRoutes)
    {
        if (is_string($invalidationRoutes))
        {
            $invalidationRoutes = array($invalidationRoutes);
        }

        array_push($this->rules, array(
            'method' => $method,
            'trigger' => $triggerRoute,
            'routes' => $invalidationRoutes)
        );
    }

    public function checkInvalidation()
    {
        $currentRoute = Request::path();
        $currentMethod = strtolower(Request::getMethod());

        foreach ($this->rules as $rule)
        {
            $pattern = $this->stringToRegex($rule['trigger']);
            if ($currentMethod == strtolower($rule['method'])
                && preg_match($pattern, $currentRoute))
            {
                foreach ($rule['routes'] as $route)
                {
                    $this->invalidate($route);
                }
            }
        }
    }

    public function invalidate($route)
    {
        $routesToInvalidate = explode(';', MemcachedInstance::get($route));
        foreach ($routesToInvalidate as $cacheKey) {
            MemcachedInstance::forget($cacheKey);
        }
    }

    private function stringToRegex($string)
    {
        return '`' . $string . '`';
    }

}
