<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class StoreTest extends TestCase
{

    public function setUp() 
    {
        parent::setUp();

        Route::get('hello', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        $this->app['router']->enableFilters();
        
        $memcachedMock = m::mock(array('setOption' => null));
        \MemcachedInstance::shouldReceive('getMemcached')
                            ->once()
                            ->andReturn($memcachedMock);
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

    public function testStoring()
    {
        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', 'Hello World', 0)
                            ->once();
        
        $this->call('GET', 'hello');
    }

    public function testStoringWithPrependingSlash()
    {
        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', 'Hello World', 0)
                            ->once();
        
        $this->call('GET', '/hello');
    }

}
