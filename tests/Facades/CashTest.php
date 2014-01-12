<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class CashTest extends TestCase
{
    public function setUp() 
    {
        parent::setUp();

        $this->invalidator = m::mock('Schickling\Cash\InvalidatorInterface');
        $this->app->instance('Schickling\Cash\InvalidatorInterface', $this->invalidator);
    }

    protected function getPackageProviders()
    {
        return array('Schickling\Cash\CashServiceProvider');
    }

    public function testSimpleRule()
    {
        Cash::rule('get', 'some/route', 'some/other/route');
    }

}
