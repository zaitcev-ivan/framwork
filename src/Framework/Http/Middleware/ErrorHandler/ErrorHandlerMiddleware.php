<?php

namespace Framework\Http\Middleware\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ErrorHandlerMiddleware
 * @package Framework\Http\Middleware\ErrorHandler
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private $responseGenerator;

    /**
     * ErrorHandlerMiddleware constructor.
     * @param ErrorResponseGenerator $responseGenerator
     */
    public function __construct(ErrorResponseGenerator $responseGenerator)
    {
        $this->responseGenerator = $responseGenerator;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->responseGenerator->generate($e, $request);
        }
    }
}