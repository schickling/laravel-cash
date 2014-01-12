<?php namespace Schickling\Cash;

use App;
use Route;

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

    }

}
