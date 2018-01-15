<?php

namespace Curve;


use Curve\Exception\ConfigNotFound;

class Config
{
    /**
     * Get a param from the config
     *
     * @todo Use a proper config system instead of $GLOBALS
     *
     * @param $paramName
     * @return mixed
     * @throws ConfigNotFound
     */
    public static function getConfigParam($paramName)
    {
        list($prefix, $name) = explode('.', $paramName);

        if (!array_key_exists($prefix, $GLOBALS['config']) || !array_key_exists($name, $GLOBALS['config'][$prefix])) {
            throw new ConfigNotFound($paramName);
        }

        return $GLOBALS['config'][$prefix][$name];
    }

    /**
     * Are we in a debug environment?
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return !empty($GLOBALS['debug']);
    }
}