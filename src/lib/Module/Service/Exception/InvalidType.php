<?php

namespace Curve\Module\Service\Exception;


use Throwable;

class InvalidType extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = 'Invalid type: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}