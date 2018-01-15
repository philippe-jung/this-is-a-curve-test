<?php

require_once(__DIR__ . '/bootstrap.php');

try {

    // simple logic to call the correct module dispatcher based on the URL
    $component = \Curve\Router::getDispatcherFromRequest();
    $component->execute();

} catch (\Exception $e) {
    // generic error management
    http_response_code(500);
    echo '<pre>Sorry, an error has occurred.</pre>';

    if (\Curve\Config::isDebug()) {
        if (!empty($e->xdebug_message)) {
            echo '<table>' . $e->xdebug_message . '</table>';
        } else {
            var_dump($e);
        }
    }
}
