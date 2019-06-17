<?php

namespace Framework\Http\Middleware\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ErrorHandlerMiddleware
 * @package Framework\Http\Middleware\ErrorHandler
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @var callable[]
     */
    private $listeners = [];
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
            foreach ($this->listeners as $listener) {
                $listener($e, $request);
            }
            return $this->responseGenerator->generate($e, $request);
        }
    }

    public function addListener(callable $listener): void
    {
        $this->listeners[] = $listener;
    }
}