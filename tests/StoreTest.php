<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class StoreTest extends TestCase
{

    public function setUp() 
    {
        parent::setUp();

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
        Route::get('hello', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', 'Hello World', 0)
                            ->once();
        \MemcachedInstance::shouldReceive('get')
                            ->with('/hello')
                            ->once()
                            ->andReturn(null);
        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', '/hello', 0)
                            ->once();
        
        $this->call('GET', 'hello');
    }

    public function testStoringWithPrependingSlash()
    {
        Route::get('/hello', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', 'Hello World', 0)
                            ->once();
        \MemcachedInstance::shouldReceive('get')
                            ->with('/hello')
                            ->once()
                            ->andReturn(null);
        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', '/hello', 0)
                            ->once();
        
        $this->call('GET', '/hello');
    }
    
    public function testStoringWithPrependingSlashOnly()
    {
        Route::get('/', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        \MemcachedInstance::shouldReceive('put')
                            ->with('/', 'Hello World', 0)
                            ->once();
        \MemcachedInstance::shouldReceive('get')
                            ->with('/')
                            ->once()
                            ->andReturn(null);
        \MemcachedInstance::shouldReceive('put')
                            ->with('/', '/', 0)
                            ->once();
        
        $this->call('GET', '/');
    }

    public function testStoringMoreComplexUrl()
    {
        Route::get('hello/{a}/more/{b}/complex', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello/1/more/3/complex', 'Hello World', 0)
                            ->once();
        \MemcachedInstance::shouldReceive('get')
                            ->with('/hello')
                            ->once()
                            ->andReturn(null);
        \MemcachedInstance::shouldReceive('put')
                            ->with('/hello', '/hello/1/more/3/complex', 0)
                            ->once();
        
        $this->call('GET', 'hello/1/more/3/complex');
    }

    public function testStoringWithAlreadyCachedResponses()
    {
        Route::get('a/{b}', array('after' => 'cash', function()
        {
            return 'Hello World';
        }));

        \MemcachedInstance::shouldReceive('put')
                            ->with('/a/2', 'Hello World', 0)
                            ->once();
        \MemcachedInstance::shouldReceive('get')
                            ->with('/a')
                            ->once()
                            ->andReturn('/a/1');
        \MemcachedInstance::shouldReceive('put')
                            ->with('/a', '/a/1;/a/2', 0)
                            ->once();
        
        $this->call('GET', 'a/2');
    }


}
