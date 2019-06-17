<?php

use App\Http\Middleware;
use Framework\Http\Middleware\ErrorHandler\ErrorHandlerMiddleware;

/** @var \Framework\Http\Application $app */

$app->pipe(ErrorHandlerMiddleware::class);
$app->pipe(Middleware\CredentialsMiddleware::class);
$app->pipe(Middleware\ProfilerMiddleware::class);
$app->pipe(Framework\Http\Middleware\RouteMiddleware::class);
$app->pipe('cabinet', Middleware\BasicAuthMiddleware::class);
$app->pipe(Framework\Http\Middleware\DispatchMiddleware::class);