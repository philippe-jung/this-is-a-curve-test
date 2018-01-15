<?php

namespace Curve\Module\Service\Exception;


class UnsupportedMethod extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'This service does not support the ' . $message . ' method';
        parent::__construct($message, $code, $previous);
    }
}