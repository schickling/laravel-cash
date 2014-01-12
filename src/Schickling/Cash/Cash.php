<?php namespace Schickling\Cash;

use App;
use Request;

class Cash
{

    protected $rules = array();

    public function rule($method, $triggerRoute, $invalidationRoutes)
    {
        array_push($this->rules, array(
            'method' => $method,
            'trigger' => $triggerRoute,
            'routes' => $invalidationRoutes));
    }

    public function checkInvalidation()
    {
        $currentRoute = Request::path();
        $currentMethod = Request::getMethod(); 
        foreach ($this->rules as $rule)
        {
            $pattern = $this->stringToRegex($rule['trigger']);
            if ($currentMethod == $rule['method'] && preg_match($pattern, $currentRoute))
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
        
    }

    private function stringToRegex($string)
    {
        return '`' . $string . '`';
    }

}
