<?php

namespace Framework\Http\Pipeline;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LazyMiddlewareDecorator
 * @package Framework\Http\Pipeline
 */
final class LazyMiddlewareDecorator implements MiddlewareInterface
{
    private $resolver;
    private $container;
    private $service;

    public function __construct(MiddlewareResolver $resolver, ContainerInterface $container, string $service)
    {
        $this->resolver = $resolver;
        $this->container = $container;
        $this->service = $service;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = $this->resolver->resolve($this->container->get($this->service));
        return $middleware->process($request, $handler);
    }
}