<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use ReputationLoop\Router;

require ("../vendor/autoload.php");

$router = new Router();

$routes = array(
    '/' => '',
    '/test/:title' => 'Main:test@get'
);

$router->addRoutes($routes);

$router->set404Handler("Main:error404");

$router->run();
