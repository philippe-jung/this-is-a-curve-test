<?php

namespace Curve;


class Request
{
    /**
     * Get all the params of the request
     *
     * @return array
     */
    public static function getParams(): array
    {
        return $_REQUEST;
    }

    /**
     * Get the value of param $name or fallback to $default
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public static function getParam($name, $default = null)
    {
        if (array_key_exists($name, $_REQUEST)) {
            return $_REQUEST[$name];
        }

        return $default;
    }

    /**
     * Does param $name exist in the request?
     *
     * @param $name
     * @return bool
     */
    public static function hasParam($name): bool
    {
        if (array_key_exists($name, $_REQUEST)) {
            return true;
        }

        return false;
    }

    /**
     * Get the HTTP request method
     *
     * @return mixed
     */
    public static function getMethod(): string
    {
        if (Config::isDebug()) {
            // this allows to specify a method even if using only GET queries
            $method = self::getParam('method');
        }
        if (empty($method)) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return $method;
    }
}