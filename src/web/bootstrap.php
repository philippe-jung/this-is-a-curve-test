<?php

$GLOBALS['debug'] = true;

if (!empty($GLOBALS['debug'])) {
    ini_set('display_errors', true);
}

require_once(__DIR__ . '/../../vendor/autoload.php');

require_once(__DIR__ . '/../conf/config.php');
