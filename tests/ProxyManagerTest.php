<?php

namespace Softonic\ProxyBonanza\Guzzle\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Softonic\ProxyBonanza\Guzzle\Middleware\Exceptions\ProxiesNotAvailable;
use Softonic\ProxyBonanza\Guzzle\Middleware\Repositories\Proxy;

class ProxyManagerTest extends TestCase
{
    /**
     * @test
     */
    public function whenProxyBonanzaDoesNotRetrieveAProxyListItShouldThrowAnException()
    {
        $proxyBonanza = \Mockery::mock(Proxy::class);
        $handler      = function ($request, $options) {
            return 'response';
        };
        $request      = \Mockery::mock(RequestInterface::class);
        $options      = [];

        $proxyManager = new ProxyManager($proxyBonanza);

        $proxyBonanza->shouldReceive('get')
            ->once()
            ->andThrow(ProxiesNotAvailable::class);

        $this->expectException(ProxiesNotAvailable::class);

        $handler = $proxyManager($handler);
        $handler($request, $options);
    }

    /**
     * @test
     */
    public function whenProxyIsAvailableItShouldSetTheProxyInTheRequest()
    {
        $proxyBonanza = \Mockery::mock(Proxy::class);
        $handler      = function ($request, $options) {
            return $options;
        };
        $request      = \Mockery::mock(RequestInterface::class);
        $options      = [];

        $proxyManager = new ProxyManager($proxyBonanza);

        $proxyBonanza->shouldReceive('get')
            ->once()
            ->andReturn('http://user:pass@server:8125');

        $handler = $proxyManager($handler);
        $options = $handler($request, $options);

        $this->assertEquals(
            'http://user:pass@server:8125',
            $options['proxy']
        );
    }
}
