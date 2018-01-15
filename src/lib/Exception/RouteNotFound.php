<?php

namespace Curve\Exception;


class RouteNotFound extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'Route not found ' . $message;
        parent::__construct($message, $code, $previous);
    }
}