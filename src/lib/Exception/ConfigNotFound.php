<?php

namespace Curve\Exception;


class ConfigNotFound extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'Config param not found ' . $message;
        parent::__construct($message, $code, $previous);
    }
}