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
        \MemcachedInstance::shouldReceive('get')
                            ->with('some/other/route')
                            ->once()
                            ->andReturn('/some/other/route');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithArrayNotation()
    {
        Cash::rule('put', 'some/route', array('a', 'b'));
        \MemcachedInstance::shouldReceive('get')
                            ->with('a')
                            ->once()
                            ->andReturn('/a');
        \MemcachedInstance::shouldReceive('get')
                            ->with('b')
                            ->once()
                            ->andReturn('/b');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/a')
                            ->once();
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/b')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithMultipleCachedResponses()
    {
        Cash::rule('put', 'some/route', 'some/other');
        \MemcachedInstance::shouldReceive('get')
                            ->with('some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithAppendingSlash()
    {
        Cash::rule('put', 'some/route', 'some/other/');
        \MemcachedInstance::shouldReceive('get')
                            ->with('some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithAsterixNotation()
    {
        Cash::rule('put', 'some/route', 'some/other/*');
        \MemcachedInstance::shouldReceive('get')
                            ->with('some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

    public function testRuleWithMultipleAsterixNotation()
    {
        Cash::rule('put', 'some/route', 'some/other/*/random/*');
        \MemcachedInstance::shouldReceive('get')
                            ->with('some/other')
                            ->once()
                            ->andReturn('/some/other/route;/some/other/different/route');
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/route')
                            ->once();
        \MemcachedInstance::shouldReceive('forget')
                            ->with('/some/other/different/route')
                            ->once();

        $this->call('PUT', 'some/route');
    }

}
