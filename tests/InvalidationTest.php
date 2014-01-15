<?php

use Schickling\Cash\Facades\Cash;
use Orchestra\Testbench\TestCase;
use Mockery as m;

class CashTest extends TestCase
{
    public function setUp() 
    {
        parent::setUp();

        Route::any('{anything}', function() {})->where('anything', '.*');
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
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:some/other/route')
                            ->once()
                            ->andReturn('/some/other/route');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithArrayNotation()
    {
        Cash::rule('put', 'some/route', array('a', 'b'));
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:a')
                            ->once()
                            ->andReturn('/a');
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:b')
                            ->once()
                            ->andReturn('/b');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/a')
                            ->once();
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/b')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithMultipleCachedResponses()
    {
        Cash::rule('put', 'some/route', 'some/other');
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithAppendingSlash()
    {
        Cash::rule('put', 'some/route', 'some/other/');
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithAsterixNotation()
    {
        Cash::rule('put', 'some/route', 'some/other/*');
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithMultipleAsterixNotation()
    {
        Cash::rule('put', 'some/route', 'some/other/*/random/*');
        \MemcachedDriver::shouldReceive('get')
                            ->with('tag:some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedDriver::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

}
