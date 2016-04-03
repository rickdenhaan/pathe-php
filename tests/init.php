<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

/**
 * Prepares a simple autoloader for the RickDenHaan\Pathe namespace
 */

date_default_timezone_set('Europe/Amsterdam');

// handle autoloading
spl_autoload_register(
    function ($className) {
        if ($className === 'MockRequest') {
            require_once(dirname(__FILE__) . '/mock/MockRequest.php');
        } else if ($className === 'MockClient') {
            require_once(dirname(__FILE__) . '/mock/MockClient.php');
        } else if (preg_match('/^RickDenHaan\\\\Pathe/', $className)) {
            $filePath = str_replace('RickDenHaan\\Pathe\\', DIRECTORY_SEPARATOR, $className) . '.php';
            if (file_exists($filePath)) {
                require_once(dirname(__FILE__) . '/../src' . $filePath);
            }
        }
    }
);
