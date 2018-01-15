<?php

namespace Curve\Module\Service\Response;

class Response
{
    /**
     * HTTP response code
     *
     * @var integer
     */
    protected $code;

    /**
     * Json encoded data to be sent
     *
     * @var string
     */
    protected $data;

    public function __construct($data, $code)
    {
        $this->code = $code;
        $this->data = json_encode($data);
    }

    /**
     * Send code + data to the client
     */
    public function send()
    {
        http_response_code($this->code);
        echo $this->data;
    }
}