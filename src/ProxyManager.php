<?php

namespace Softonic\ProxyBonanza\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Softonic\ProxyBonanza\Guzzle\Middleware\Repositories\Proxy;

class ProxyManager
{
    /**
     * @var Proxy
     */
    private $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $proxy            = $this->proxy->get();
            $options['proxy'] = $proxy;

            return $handler($request, $options);
        };
    }
}
