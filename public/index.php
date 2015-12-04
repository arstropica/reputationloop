<?php
use ReputationLoop\Router;

require ("../vendor/autoload.php");

$router = new Router ();

$routes = array (
		'/(:id)' => 'Main:index@get',
		'/api/get(/:id)' => 'Info:get@get',
		'/api/reviews/get(/:id)' => 'Review:index@get' 
);

$router->addRoutes ( $routes );

$router->set404Handler ( "Main:error404" );

$router->run ();
