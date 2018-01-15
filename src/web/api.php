<?php

require_once(__DIR__ . '/bootstrap.php');

use Curve\Module\Service\Response\Error;
use Curve\Exception;

try {

    // simple logic to call the correct module dispatcher based on the URL
    $component = \Curve\Router::getDispatcherFromRequest();
    $component->execute();

} catch (Exception\RouteNotFound $e) {
    // no route was found
    $response = new Error('No such endpoint');
    $response->send();
} catch (\Exception $e) {
    // error management for API calls
    if (\Curve\Config::isDebug()) {
        $response = new Error(array(
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ));
    } else {
        $response = new Error('An internal error has occurred');
    }
    $response->send();
}
