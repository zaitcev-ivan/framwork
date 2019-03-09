<?php

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

# Initialization

$request = ServerRequestFactory::fromGlobals();

# Preprocessing
if (preg_match('#json#i', $request->getHeader('Content-Type'))) {
    $request = $request->withParsedBody(json_decode($request->getBody()->getContents()));
}

# Action
$name = $request->getQueryParams()['name'] ?? 'Guest';
$response = new HtmlResponse('Hello, ' . $name . '!');

# Postprocessing
$response = $response->withHeader('X-Developer', 'Zaicev');

# Sending
$emitter = new SapiEmitter();
$emitter->emit($response);


