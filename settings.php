<?php
use Model\ReputationLoop;
use Slim\Views\Twig;

$twig = new Twig ();
$model = new ReputationLoop ();

$settings = array (
		'view' => $twig,
		'templates.path' => '../View',
		'model' => $model,
);

return $settings;