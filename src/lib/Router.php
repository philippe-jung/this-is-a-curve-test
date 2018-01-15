<?php

namespace Curve;

use Curve\Exception;
use Curve\Module\DispatcherInterface;

class Router
{
    /**
     * Get the Dispatcher matching the route called
     *
     * @return DispatcherInterface
     * @throws Exception\RouteNotFound
     */
    public static function getDispatcherFromRequest(): DispatcherInterface
    {
        $route = '';
        if (!empty($_SERVER['PATH_INFO'])) {
            $route = $_SERVER['PATH_INFO'];
        }

        // remove starting / if any
        $route = ltrim($route, '/');

        // get the class name for the Dispatcher matching the route
        try {
            $className = Config::getConfigParam('routing.' . $route);
        } catch (Exception\ConfigNotFound $e) {
            throw new Exception\RouteNotFound($route);
        }

        return new $className();
    }
}