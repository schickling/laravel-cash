<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class CashTest extends TestCase
{
    public function setUp() 
    {
        parent::setUp();
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
