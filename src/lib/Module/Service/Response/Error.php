<?php

namespace Curve\Module\Service\Response;

class Error extends Response
{
    /**
     * Shortcut for response errors
     * @param $data
     * @param int $code
     */
    public function __construct($data, $code = 500)
    {
        if (is_string($data)) {
            $data = array('error' => $data);
        }

        parent::__construct($data, $code);
    }
}