<?php

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

# Initialization

$request = ServerRequestFactory::fromGlobals();


# Action

$path = $request->getUri()->getPath();

if ($path === '/') {
    $name = $request->getQueryParams()['name'] ?? 'Guest';
    $response = new HtmlResponse('Hello, ' . $name . '!');
} elseif ($path === '/about') {
    $response = new HtmlResponse('I am a simple page');
} else {
    $response = new JsonResponse(['error' => 'Undefined page'], 404);
}


# Postprocessing
$response = $response->withHeader('X-Developer', 'Zaicev');

# Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


