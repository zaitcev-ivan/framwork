<?php

namespace Framework\Http;

class Request
{
    public function getQueryParams(): array
    {
        return $_GET;
    }

    public function getParsedBody()
    {
        return $_POST ?: null;
    }

    public function getBody()
    {
        return file_get_contents('php://input');
    }
}