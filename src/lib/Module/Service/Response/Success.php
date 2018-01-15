<?php

namespace Curve\Module\Service\Response;

class Success extends Response
{
    /**
     * Shortcut for success response
     * @param $data
     * @param int $code
     */
    public function __construct($data, $code = 200)
    {
        parent::__construct($data, $code);
    }
}