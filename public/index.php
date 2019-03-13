<?php

use App\Http\Action\AboutAction;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Action\Blog\IndexAction;
use App\Http\Action\Blog\ShowAction;
use App\Http\Action\CabinetAction;
use App\Http\Action\HelloAction;
use App\Http\Middleware\NotFoundHandler;
use App\Http\Middleware\ProfilerMiddleware;
use Aura\Router\RouterContainer;
use Framework\Http\ActionResolver;
use Framework\Http\Pipeline\Pipeline;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$params = [
    'users' => ['admin' => 'password'],
];

# Initialization
$aura = new RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', HelloAction::class);
$routes->get('about', '/about', AboutAction::class);
$routes->get('cabinet', '/cabinet', function (ServerRequestInterface $request) use ($params) {
    $pipeline = new Pipeline();
    $pipeline->pipe(new ProfilerMiddleware());
    $pipeline->pipe(new BasicAuthMiddleware($params['users']));
    $pipeline->pipe(new CabinetAction());

    return $pipeline($request, new NotFoundHandler());
});
$routes->get('blog', '/blog', IndexAction::class);
$routes->get('blog_show', '/blog/{id}', ShowAction::class)->tokens(['id' => '\d+']);

$router = new AuraRouterAdapter($aura);
$resolver = new ActionResolver();

### Running
$request = ServerRequestFactory::fromGlobals();
try {
    $result = $router->match($request);
    foreach ($result->getAttributes() as $attribute => $value) {
        $request = $request->withAttribute($attribute, $value);
    }
    $handler = $result->getHandler();
    $action = $resolver->resolve($handler);
    $response = $action($request);
} catch (RequestNotMatchedException $e){
    $handler = new NotFoundHandler();
    $response = $handler($request);
}


# Postprocessing
$response = $response->withHeader('X-Developer', 'Zaicev');

# Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


