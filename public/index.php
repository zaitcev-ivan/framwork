<?php

use App\Http\Action\AboutAction;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Action\Blog\IndexAction;
use App\Http\Action\Blog\ShowAction;
use App\Http\Action\CabinetAction;
use App\Http\Action\HelloAction;
use App\Http\Middleware\CredentialsMiddleware;
use App\Http\Middleware\ErrorHandlerMiddleware;
use App\Http\Middleware\NotFoundHandler;
use App\Http\Middleware\ProfilerMiddleware;
use Framework\Container\Container;
use Framework\Http\Middleware\DispatchMiddleware;
use Framework\Http\Middleware\RouteMiddleware;
use Aura\Router\RouterContainer;
use Framework\Http\Application;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

# Configuration

$container = new Container();
$container->set('config', [
    'debug' => true,
    'users' => ['admin' => 'password'],
]);

$container->set('middleware.basic_auth', function (Container $container) {
    return new BasicAuthMiddleware($container->get('config')['users']);
});
$container->set('middleware.error_handler', function (Container $container) {
    return new ErrorHandlerMiddleware($container->get('config')['debug']);
});

### Initialization
$aura = new RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', HelloAction::class);
$routes->get('about', '/about', AboutAction::class);
$routes->get('cabinet', '/cabinet', CabinetAction::class);
$routes->get('blog', '/blog', IndexAction::class);
$routes->get('blog_show', '/blog/{id}', ShowAction::class)->tokens(['id' => '\d+']);

$router = new AuraRouterAdapter($aura);
$resolver = new MiddlewareResolver();
$app = new Application($resolver, new NotFoundHandler(), new Response());

$app->pipe($container->get('middleware.error_handler'));
$app->pipe(CredentialsMiddleware::class);
$app->pipe(ProfilerMiddleware::class);
$app->pipe(new RouteMiddleware($router));
$app->pipe('cabinet', $container->get('middleware.basic_auth'));
$app->pipe(new DispatchMiddleware($resolver));

### Running
$request = ServerRequestFactory::fromGlobals();
$response = $app->run($request, new Response(), new Response());

# Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


