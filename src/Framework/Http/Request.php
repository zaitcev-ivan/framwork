<?php

namespace Framework\Http;

/**
 * Class Request
 * @package Framework\Http
 */
class Request
{
    private $queryParams;
    private $parsedBody;

    /**
     * Request constructor.
     * @param array $queryParams
     * @param array $parsedBody
     */
    public function __construct(array $queryParams = [], $parsedBody = null)
    {
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): self
    {
        $request = clone $this;
        $request->queryParams = $query;
        return $request;
    }

    /**
     * @return array
     */
    public function getParsedBody(): ?array
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): self
    {
        $request = clone $this;
        $request->parsedBody = $data;
        return $this;
    }
}