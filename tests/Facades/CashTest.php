<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class CashTest extends TestCase
{
    public function setUp() 
    {
        parent::setUp();

        Route::any('{anything}', function()
        {
            return 'Hello World';
        })->where('anything', '.*');

        $this->app['router']->enableFilters();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    protected function getPackageProviders()
    {
        return array('Schickling\Cash\CashServiceProvider');
    }

    public function testSimpleRule()
    {
        Cash::rule('put', 'some/route', 'some/other/route');
        \MemcachedInstance::shouldReceive('test')->once();

        $this->call('PUT', 'some/route');
    }

}
