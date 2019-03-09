<?php

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseSender
 * @package Framework\Http
 */
class ResponseSender
{
    /**
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response): void
    {
        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        echo $response->getBody()->getContents();
    }
}