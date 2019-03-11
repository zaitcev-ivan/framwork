<?php

namespace Tests\Framework\Http;

use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\RouteCollection;
use Framework\Http\Router\SimpleRouter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterTest extends TestCase
{
    public function testCorrectMethod(): void
    {
        $routes = new RouteCollection();

        $routes->get($nameGet = 'blog', '/blog', $handlerGet = 'handler_get');
        $routes->post($namePost = 'blog_edit', '/blog', $handlerPost = 'handler_post');

        $router = new SimpleRouter($routes);

        $result = $router->match($this->buildRequest('GET', '/blog'));
        self::assertEquals($nameGet, $result->getName());
        self::assertEquals($handlerGet, $result->getHandler());

        $result = $router->match($this->buildRequest('POST', '/blog'));
        self::assertEquals($namePost, $result->getName());
        self::assertEquals($handlerPost, $result->getHandler());
    }

    public function testMissingMethod(): void
    {
        $routes = new RouteCollection();

        $routes->post('blog', '/blog', 'handler_post');

        $router = new SimpleRouter($routes);

        $this->expectException(RequestNotMatchedException::class);
        $router->match($this->buildRequest('DELETE', '/blog'));
    }

    public function testIncorrectAttributes(): void
    {
        $routes = new RouteCollection();

        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new SimpleRouter($routes);

        $this->expectException(RequestNotMatchedException::class);
        $router->match($this->buildRequest('GET', '/blog/slug'));
    }

    public function testGenerate(): void
    {
        $routes = new RouteCollection();

        $routes->get('blog', '/blog', 'handler_get');
        $routes->get('blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new SimpleRouter($routes);

        self::assertEquals('/blog', $router->generate('blog'));
        self::assertEquals('/blog/5', $router->generate('blog_show', ['id' => 5]));
    }

    public function testGenerateMissingAttributes(): void
    {
        $routes = new RouteCollection();

        $routes->get($name = 'blog_show', '/blog/{id}', 'handler', ['id' => '\d+']);

        $router = new SimpleRouter($routes);

        $this->expectException(\InvalidArgumentException::class);
        $router->generate('blog_show', ['slug' => 'post']);
    }

    /**
     * @param $method
     * @param $uri
     * @return ServerRequest
     */
    private function buildRequest($method, $uri): RequestInterface
    {
        return (new ServerRequest())
            ->withMethod($method)
            ->withUri(new Uri($uri));
    }
}