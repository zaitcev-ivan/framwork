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
use Framework\Http\Application;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Router;
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

$container->set(Application::class, function (Container $container) {
    return new Application(
        $container->get(MiddlewareResolver::class),
        new NotFoundHandler(),
        new Response()
    );
});

$container->set(BasicAuthMiddleware::class, function (Container $container) {
    return new BasicAuthMiddleware($container->get('config')['users']);
});
$container->set(ErrorHandlerMiddleware::class, function (Container $container) {
    return new ErrorHandlerMiddleware($container->get('config')['debug']);
});

$container->set(DispatchMiddleware::class, function (Container $container) {
    return new DispatchMiddleware($container->get(MiddlewareResolver::class));
});

$container->set(MiddlewareResolver::class, function () {
    return new MiddlewareResolver();
});

$container->set(RouteMiddleware::class, function (Container $container) {
    return new RouteMiddleware($container->get(Router::class));
});

$container->set(Router::class, function () {
    $aura = new Aura\Router\RouterContainer();
    $routes = $aura->getMap();
    $routes->get('home', '/', HelloAction::class);
    $routes->get('about', '/about', AboutAction::class);
    $routes->get('cabinet', '/cabinet', CabinetAction::class);
    $routes->get('blog', '/blog', IndexAction::class);
    $routes->get('blog_show', '/blog/{id}', ShowAction::class)->tokens(['id' => '\d+']);
    return new AuraRouterAdapter($aura);
});

### Initialization

/** @var Application $app */
$app = $container->get(Application::class);

$app->pipe($container->get(ErrorHandlerMiddleware::class));
$app->pipe(CredentialsMiddleware::class);
$app->pipe(ProfilerMiddleware::class);
$app->pipe($container->get(Framework\Http\Middleware\RouteMiddleware::class));
$app->pipe('cabinet', $container->get(BasicAuthMiddleware::class));
$app->pipe($container->get(Framework\Http\Middleware\DispatchMiddleware::class));

### Running
$request = ServerRequestFactory::fromGlobals();
$response = $app->run($request, new Response());

# Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


